@php
    /** @var \App\Models\AcademyParent|null $parent */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Student> $students */
    $isEdit = isset($parent);
    $selectedIds = old('student_ids', $isEdit ? $parent->students->pluck('id')->all() : []);
@endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700" for="full_name">Full name</label>
        <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $parent->full_name ?? '') }}"
               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none" required>
        @error('full_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $parent->email ?? '') }}"
               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none" required>
        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700" for="phone">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $parent->phone ?? '') }}"
               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    @if (! $isEdit)
        <div>
            <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password"
                   class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none" required>
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none" required>
        </div>
    @else
        <div class="md:col-span-2 rounded-md border border-dashed border-gray-300 bg-gray-50 p-3">
            <p class="text-xs text-gray-600">Leave password fields empty to keep the current password.</p>
            <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="password">New password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password"
                           class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="password_confirmation">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                           class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                </div>
            </div>
        </div>
    @endif

    <div>
        <label class="block text-sm font-medium text-gray-700" for="country">Country</label>
        <input id="country" name="country" type="text" value="{{ old('country', $parent->country ?? '') }}"
               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
        @error('country')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700" for="timezone">Timezone</label>
        <input id="timezone" name="timezone" type="text" placeholder="e.g. Asia/Dubai"
               value="{{ old('timezone', $parent->timezone ?? '') }}"
               class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
        @error('timezone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700" for="status">Status</label>
        <select id="status" name="status" required
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
            @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $val => $label)
                <option value="{{ $val }}" @selected(old('status', $parent->status ?? 'active') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700" for="student_ids">Linked students (active)</label>
        <select id="student_ids" name="student_ids[]" multiple size="8"
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
            @foreach ($students as $stu)
                <option value="{{ $stu->id }}" @selected(in_array($stu->id, $selectedIds, true))>
                    {{ $stu->public_id }} — {{ $stu->full_name }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple. Duplicate pairings are prevented by the system.</p>
        @error('student_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('student_ids.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700" for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3"
                  class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">{{ old('notes', $parent->notes ?? '') }}</textarea>
        @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
