<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;

class PublishScheduledEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically publish events that are scheduled for publishing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scheduledEvents = Event::scheduledForPublishing()->get();
        
        if ($scheduledEvents->isEmpty()) {
            $this->info('No events scheduled for publishing at this time.');
            return;
        }
        
        $publishedCount = 0;
        
        foreach ($scheduledEvents as $event) {
            $event->update(['publish_status' => 'published']);
            $publishedCount++;
            $this->info("Published: {$event->title}");
        }
        
        $this->info("Successfully published {$publishedCount} scheduled events.");
    }
}