<?php

namespace App\Console\Commands;

use App\Models\Dette;
use App\Enums\StatusDetteEnum;
use Illuminate\Console\Command;

class FixDetteStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dette:fix-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige automatiquement les statuts des dettes en fonction du montant restant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Correction des statuts des dettes...');

        $dettes = Dette::all();
        $updated = 0;

        foreach ($dettes as $dette) {
            $oldStatus = $dette->status;
            $expectedStatus = ($dette->montantRestant == 0) ? StatusDetteEnum::SOLDE : StatusDetteEnum::NON_SOLDE;

            if ($dette->status !== $expectedStatus) {
                $dette->status = $expectedStatus;
                $dette->save();
                $updated++;
                $this->line("Dette #{$dette->id}: {$oldStatus} → {$expectedStatus}");
            }
        }

        if ($updated > 0) {
            $this->success("{$updated} dette(s) corrigée(s).");
        } else {
            $this->info('Tous les statuts sont déjà corrects.');
        }

        return Command::SUCCESS;
    }
}
