<?php

namespace App\Jobs;

use App\Models\Comment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeCommentSentiment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected Comment $comment;

    /**
     * Create a new job instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('AnalyzeCommentSentiment job started for comment id: ' . $this->comment->id);
        $commentText = escapeshellarg($this->comment->body);
        $path = base_path('python/sentimentAnalyzer.py');
        $command = 'python ' . escapeshellarg($path) . ' ' . $commentText;
        $sentiment = strtolower(trim(shell_exec($command)));
        $output = shell_exec($command);

        logger()->info('Sentiment Analysis Output:', ['output' => $output]);

        $sentiment = strtolower(trim($output));

        if (in_array($sentiment, ['positive', 'negative', 'neutral'])) {
            $this->comment->update([
                "sentiment" => $sentiment,
            ]);
            logger()->info('Sentiment saved:', ['sentiment' => $sentiment]);
        } else {
            logger()->warning('Invalid sentiment result:', ['output' => $output]);
        }
    }
}
