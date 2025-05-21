<?php

namespace Amplify\System\Sayt\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReConfigSaytStore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shopPagePrefix = config('amplify.basic.shop_page_prefix');
        $shopUrl = config('app.url') . '/' . $shopPagePrefix;

        $compiledStoreScript = view('sayt::store-template', compact('shopUrl'));
        file_put_contents(public_path('sayt-store.js'), $compiledStoreScript);
    }
}
