<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppNotificationService;

class AutoRejectExpiredApprovals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absen:auto-reject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto reject expired approval tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        
        // Ambil semua token yang pending dan sudah expired
        $expiredTokens = DB::table('approval_tokens')
            ->where('status', 'pending')
            ->where('expired_at', '<', $now)
            ->get();
        
        if ($expiredTokens->isEmpty()) {
            $this->info('No expired approvals found.');
            return 0;
        }
        
        $waService = new WhatsAppNotificationService();
        $processedCount = 0;
        $errorCount = 0;
        
        foreach ($expiredTokens as $token) {
            try {
                DB::beginTransaction();
                
                // Update token status
                DB::table('approval_tokens')
                    ->where('id', $token->id)
                    ->update([
                        'status' => 'expired',
                        'processed_at' => $now,
                    ]);
                
                // Process berdasarkan type
                if ($token->type === 'izin') {
                    // Update formulir_izin
                    $formulir = DB::table('formulir_izin')
                        ->where('id', $token->reference_id)
                        ->first();
                    
                    if ($formulir) {
                        DB::table('formulir_izin')
                            ->where('id', $token->reference_id)
                            ->update([
                                'auto_reject' => 1,
                                'is_approval' => false,
                                'timestamp_reject' => $now,
                            ]);
                        
                        // Hapus juga dari tabel absen (izin tidak diakui)
                        DB::table('absen')
                            ->where('izin_id', $token->reference_id)
                            ->update([
                                'status_izin' => null,
                                'izin_id' => null,
                            ]);
                        
                        // Get data untuk notifikasi
                        $user = DB::table('users')->where('nip', $token->nip_pengaju)->first();
                        
                        // Kirim notifikasi ke user
                        if ($user) {
                            $waService->sendApprovalResultIzinToUser([
                                'nip_user' => $token->nip_pengaju,
                                'nama_user' => $user->nama,
                                'status' => $formulir->status,
                                'from' => $formulir->from,
                                'to' => $formulir->to,
                                'alasan' => $formulir->alasan ?? '-',
                                'result' => 'rejected',
                                'processed_by' => 'Sistem (Expired)',
                            ]);
                            
                            $this->line("  [Izin] Rejected: {$user->nama} ({$formulir->status})");
                        }
                    }
                } elseif ($token->type === 'pulang') {
                    // Update absen - hapus jam_temp
                    $absen = DB::table('absen')
                        ->where('id', $token->reference_id)
                        ->first();
                    
                    if ($absen && $absen->jam_temp) {
                        DB::table('absen')
                            ->where('id', $token->reference_id)
                            ->update([
                                'jam_temp' => null,
                                'alasan_pulang' => null,
                            ]);
                        
                        // Get data untuk notifikasi
                        $user = DB::table('users')->where('nip', $token->nip_pengaju)->first();
                        
                        // Kirim notifikasi ke user
                        if ($user) {
                            $waService->sendApprovalResultPulangToUser([
                                'nip_user' => $token->nip_pengaju,
                                'nama_user' => $user->nama,
                                'tanggal' => $absen->tanggal,
                                'jam_pulang' => $absen->jam_temp,
                                'alasan' => $absen->alasan_pulang ?? '-',
                                'result' => 'rejected',
                                'processed_by' => 'Sistem (Expired)',
                            ]);
                            
                            $this->line("  [Pulang] Rejected: {$user->nama} ({$absen->tanggal})");
                        }
                    }
                }
                
                DB::commit();
                $processedCount++;
                
            } catch (\Exception $e) {
                DB::rollBack();
                $errorCount++;
                $this->error("  Error processing token {$token->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Processed: {$processedCount} approvals auto-rejected, {$errorCount} errors.");
        
        return 0;
    }
}
