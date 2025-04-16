<?php

namespace App\Filament\Widgets;

use App\Models\listing;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use NumberFormatter;

class StatsOverview extends BaseWidget
{
    private function getPercentage(int $from, int $to){
        // If both values are 0, there's no change (0%)
        if ($from === 0 && $to === 0) {
            return 0;
        }
        
        // If previous value was 0, it's a 100% increase
        if ($from === 0) {
            return 100;
        }
        
        // Normal percentage calculation
        return ($to - $from) / (($to + $from) / 2) * 100;
    }
    
    protected function getStats(): array
    {
        $newListing = Listing::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
            
        // Current month transactions
        $transactionQuery = Transaction::whereStatus('approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);
        $transaction = $transactionQuery->count();
        $currentRevenue = $transactionQuery->sum('total_price');
        
        // Previous month transactions
        $prevTransactionQuery = Transaction::whereStatus('approved')
            ->whereMonth('created_at', Carbon::now()->subMonth())
            ->whereYear('created_at', Carbon::now()->subYear()->year);
        $prevTransaction = $prevTransactionQuery->count();
        $prevRevenue = $prevTransactionQuery->sum('total_price');
        
        // Calculate percentages
        $transactionPercentage = $this->getPercentage($prevTransaction, $transaction);
        $revenuePercentage = $this->getPercentage($prevRevenue, $currentRevenue);

        return [
            Stat::make('New listing of the month', $newListing),
            Stat::make('New Transaction of the month', $transaction)
                ->description($transactionPercentage > 0 ? "{$transactionPercentage}% increased" : "{$transactionPercentage}% decreased")
                ->descriptionIcon($transactionPercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($transactionPercentage > 0 ? 'success' : 'danger'),
            Stat::make('Revenue of the month', Number::currency($currentRevenue, 'IDR'))
                ->description($revenuePercentage > 0 ? "{$revenuePercentage}% increased" : "{$revenuePercentage}% decreased")
                ->descriptionIcon($revenuePercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenuePercentage > 0 ? 'success' : 'danger')
        ];
    }
}