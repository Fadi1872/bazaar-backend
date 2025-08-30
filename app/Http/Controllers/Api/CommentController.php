<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Services\CommentService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    protected CommentService $commentService;
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        try {
            $comment = $this->commentService->update($comment, $request->validated());
            return $this->successResponse("comment updated successfully!", $comment);
        } catch (Exception $e) {
            return $this->errorResponse("failed to update comment.");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        try {
            $this->commentService->delete($comment);
            return $this->successResponse("comment deleted successfully!");
        } catch (Exception $e) {
            return $this->errorResponse("failed to delete comment");
        }
    }

    /**
     * like the comment
     */
    public function like(Comment $comment)
    {
        $this->authorize('like', Comment::class);

        try {
            $this->commentService->like($comment);
            return $this->successResponse("comment liked successfully!");
        } catch (Exception $e) {
            return $this->errorResponse("failed to like the comment.");
        }
    }

    /**
     * unlike the comment
     */
    public function unlike(Comment $comment)
    {
        $this->authorize('unlike', Comment::class);

        try {
            $this->commentService->unlike($comment);
            return $this->successResponse("comment unliked successfully!");
        } catch (Exception $e) {
            return $this->errorResponse("failed to unlike the comment.");
        }
    }
}
