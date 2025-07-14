<?php

namespace App\Services;

use app\Contracts\Roles;
use App\Contracts\StorageInterface;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AuthService
{
    protected StorageInterface $storage;
    protected ImageStorage $imageStorage;
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
        $this->imageStorage = new ImageStorage();
    }

    /**
     * register the user in
     * 
     * @param array $data
     * @param UploadedFile $image
     * @return array
     */
    public function rigister(array $data, UploadedFile $image)
    {
        $appSource = $data['app_source'];
        unset($data['app_source']);
        $user = $this->storage->store($data);
        if ($image) {
            $path = $this->imageStorage->uploadImage($image, ImageStorage::PROFILE_IMAGE);
            $user->image()->create([
                'path' => $path
            ]);
        }
        if ($appSource == "admin")
            $user->assignRole(Roles::SELLER);
        else
            $user->assignRole(Roles::INSPECTOR);
        $token = $user->createToken(config('app.name', 'Bazaar'))->plainTextToken;
        return [
            'token' => $token,
            'user' => new UserResource($user->load('image')),
        ];
    }

    /**
     * log the user in
     * 
     * @param string $emial
     * @param string $password
     * @return array
     * 
     * @throws Exception
     */
    public function login(string $email, string $password, string $appSource)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password))
            throw new Exception("The provided credentials are not valid");

        $role = $appSource === 'admin' ? Roles::SELLER : Roles::INSPECTOR;
        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }
        $token = $user->createToken(config('app.name', 'TaskManagement'))->plainTextToken;

        return [
            'user' => new UserResource($user->load('image')),
            'token' => $token
        ];
    }

    /**
     * log the user out
     * 
     * @param User $user
     * @return void
     */
    public function logout(User $user)
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * update the user
     * 
     * @param User $user, array $data
     * @return bool
     */
    public function update(User $user, array $data)
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        $updated = $this->storage->update($user, $data);
        $imageUrl = '';
        if ($image) {
            if ($user->image && $user->image->path) {
                $this->imageStorage->deleteImage($user->image->path);
                $user->image()->delete();
            }
            $path = $this->imageStorage->uploadImage($image, ImageStorage::PROFILE_IMAGE);
            $user->image()->create([
                'path' => $path
            ]);
            $imageUrl = $this->imageStorage->getUrl($path);
        }
        return $updated;
    }
}
