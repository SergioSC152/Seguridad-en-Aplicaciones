<?php

namespace App\Services;

use App\Models\LivestockBatch;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class LivestockBatchService
{
    public function paginateFor(User $user, array $filters = []): LengthAwarePaginator
    {
        return LivestockBatch::query()
            ->with('category')
            ->where('user_id', $user->id)
            ->when($filters['q'] ?? null, function ($query, string $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('code', 'like', "%{$term}%")
                        ->orWhere('ear_tag', 'like', "%{$term}%")
                        ->orWhere('farm_name', 'like', "%{$term}%");
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $categoryId) => $query->where('livestock_category_id', $categoryId))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();
    }

    public function create(User $user, array $data): LivestockBatch
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        $path = null;

        try {
            if ($image) {
                $path = $image->store('livestock-batches/'.$user->id, 'public');
                if (! $path) {
                    throw ValidationException::withMessages(['image' => 'No se pudo guardar la imagen del lote. Inténtalo nuevamente.']);
                }
                $data['image_path'] = $path;
                $data['image_url'] = '/storage/'.$path;
            }

            return DB::transaction(function () use ($user, $data) {
                $data = $this->resolveCategory($user, $data);

                return $user->livestockBatches()->create($data);
            });
        } catch (Throwable $exception) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            throw $exception;
        }
    }

    public function update(LivestockBatch $batch, array $data): LivestockBatch
    {
        $image = $data['image'] ?? null;
        $remove = (bool) ($data['remove_image'] ?? false);
        unset($data['image'], $data['remove_image']);
        $oldPath = $batch->image_path;
        $newPath = null;

        try {
            if ($image) {
                $newPath = $image->store('livestock-batches/'.$batch->user_id, 'public');
                if (! $newPath) {
                    throw ValidationException::withMessages(['image' => 'No se pudo guardar la imagen del lote. Inténtalo nuevamente.']);
                }
                $data['image_path'] = $newPath;
                $data['image_url'] = '/storage/'.$newPath;
            } elseif ($remove) {
                $data['image_path'] = null;
                $data['image_url'] = null;
            }

            DB::transaction(function () use ($batch, $data) {
                $batch->update($this->resolveCategory($batch->user, $data));
                if(array_key_exists('average_weight_kg',$data) && $data['average_weight_kg'] !== null) \App\Models\LivestockMeasurement::create(['user_id'=>$batch->user_id,'livestock_batch_id'=>$batch->id,'average_weight_kg'=>$data['average_weight_kg'],'rfid'=>$batch->rfid,'source'=>'manual']);
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        if (($newPath || $remove) && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $batch->refresh();
    }

    public function delete(LivestockBatch $batch): void
    {
        if (\App\Models\Quote::where('livestock_batch_id',$batch->id)->where('status','accepted')->exists()) throw ValidationException::withMessages(['batch'=>'El lote tiene contratos aceptados; conserva su historial e inactívalo.']);
        if (\App\Models\Auction::where('livestock_batch_id',$batch->id)->exists()) throw ValidationException::withMessages(['batch'=>'El lote tiene remates asociados; puedes inactivarlo.']);
        $path = $batch->image_path;
        DB::transaction(fn () => $batch->delete());
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function resolveCategory(User $user, array $data): array
    {
        if (filled($data['new_category_name'] ?? null)) {
            $category = $user->livestockCategories()->firstOrCreate(
                ['name' => trim($data['new_category_name'])],
                ['active' => true]
            );
            $data['livestock_category_id'] = $category->id;
        }
        unset($data['new_category_name']);

        return $data;
    }
}
