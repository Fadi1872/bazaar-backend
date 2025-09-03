<?php

namespace App\Services;

use App\Jobs\AnalyzeCommentSentiment;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CommentService
{

    /**
     * create new comment and analyze the sentiment
     * 
     * @param array $data - comment body and rating
     * @return Comment
     */
    public function create(Model $model, array $data)
    {
        $data['user_id'] = Auth::id();
        $comment = $model->comments()->create($data);

        if (isset($model->rating)) {
            $newCount = $model->rating_count + 1;
            $newRating = (($model->rating * $model->rating_count) + $comment->rating) / $newCount;

            $model->update([
                'rating_count' => $newCount,
                'rating' => round($newRating, 1)
            ]);
        }

        AnalyzeCommentSentiment::dispatch($comment);
        return $comment;
    }

    /**
     * get all the comments associated with a specific model
     * 
     * @param array $data - comment body and rating
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(Model $model)
    {
        $userId = Auth::id();

        $comments = $model->comments()
            ->with(['user', "user.image"])
            ->withCount('likes')
            ->when($userId, function ($query) use ($userId) {
                $query->withExists([
                    'likes as is_liked' => function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    }
                ]);
            }) 
            ->orderByRaw("FIELD(sentiment, 'positive', 'neutral', 'negative')")
            ->paginate(15);

        return $comments;
    }

    /**
     * update the comment
     * 
     * @param Comment $comment
     * @param array $data
     */
    public function update(Comment $comment, array $data)
    {
        $oldRating = $comment->rating;
        $comment->update($data);
        $newRating = $comment->rating;

        AnalyzeCommentSentiment::dispatch($comment);

        $model = $comment->commentable;

        if (isset($model->rating) && isset($model->ratings_count) && $oldRating !== $newRating) {
            $total = $model->rating * $model->ratings_count;
            $newTotal = $total - $oldRating + $newRating;
            $newAvg = $newTotal / $model->ratings_count;

            $model->update([
                'rating' => round($newAvg, 1)
            ]);
        }
        
        return $comment;
    }

    /**
     * delete the comment
     * 
     * @param Comment $comment
     */
    public function delete(Comment $comment)
    {
        $comment->delete();
    }

    /**
     * like a comment
     * 
     * @param Comment $comment
     */
    public function like(Comment $comment)
    {
        $user = Auth::user();

        if (!$comment->isLikedBy($user))
            $comment->likes()->attach($user->id);
    }

    /**
     * unlike a comment
     * 
     * @param Comment $comment
     */
    public function unlike(Comment $comment)
    {
        $user = Auth::user();

        if ($comment->isLikedBy($user))
            $comment->likes()->detach($user->id);
    }
}
