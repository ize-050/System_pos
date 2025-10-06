<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate-daily {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily report snapshot for sales and purchases';

    protected $reportService;

    /**
     * Create a new command instance.
     */
    public function __construct(ReportService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->argument('date') ?? Carbon::yesterday()->toDateString();

        $this->info("Generating daily report for {$date}...");

        try {
            $report = $this->reportService->generateDailyReport($date);

            $this->info("✓ Daily report generated successfully!");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Report Date', $report->report_date],
                    ['Sales Total', '฿' . number_format($report->sales_total, 2)],
                    ['Purchase Total', '฿' . number_format($report->purchase_total, 2)],
                    ['Profit/Loss', '฿' . number_format($report->profit_loss, 2)],
                    ['Transaction Count', number_format($report->transaction_count)],
                    ['Generated At', $report->generated_at],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Failed to generate daily report: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
