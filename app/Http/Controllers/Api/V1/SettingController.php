<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SettingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return SettingResource::collection(Setting::query()->when($request->boolean('public'), fn ($query) => $query->where('is_public', true))->orderBy('group')->orderBy('key')->get());
    }
    public function store(UpsertSettingRequest $request): SettingResource { return new SettingResource(Setting::updateOrCreate(['key' => $request->validated('key')], $request->validated())); }
    public function show(Setting $setting): SettingResource { return new SettingResource($setting); }
    public function update(UpsertSettingRequest $request, Setting $setting): SettingResource { $setting->update($request->validated()); return new SettingResource($setting->refresh()); }
    public function destroy(Setting $setting): JsonResponse { $setting->delete(); return response()->json(status: 204); }
}
