@extends('admin.layouts.app')

@section('title', 'Activity Log')

@section('content')
    <div class="mb-4">
        <h3 class="text-lg font-semibold">Activity Log</h3>
        <p class="text-sm text-zinc-400">Track admin actions across the system</p>
    </div>

    {{-- Filters --}}
    <div class="mb-6 rounded-2xl border border-white/10 bg-zinc-900/60 p-4">
        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="flex flex-col sm:flex-row gap-3">
            {{-- Action Filter --}}
            <div class="sm:w-40">
                <label for="action" class="sr-only">Filter by action</label>
                <select id="action"
                        name="action"
                        class="w-full rounded-lg bg-zinc-800 border border-white/10 px-3 py-2 text-sm text-white outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50 transition-colors">
                    <option value="">All Actions</option>
                    <option value="created" @selected(request('action') === 'created')>Created</option>
                    <option value="updated" @selected(request('action') === 'updated')>Updated</option>
                    <option value="deleted" @selected(request('action') === 'deleted')>Deleted</option>
                </select>
            </div>

            {{-- Model Type Filter --}}
            <div class="sm:w-44">
                <label for="model_type" class="sr-only">Filter by type</label>
                <select id="model_type"
                        name="model_type"
                        class="w-full rounded-lg bg-zinc-800 border border-white/10 px-3 py-2 text-sm text-white outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50 transition-colors">
                    <option value="">All Types</option>
                    @foreach($modelTypes as $type)
                        <option value="{{ $type['value'] }}" @selected(request('model_type') === $type['value'])>
                            {{ $type['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-700 text-white px-4 py-2 text-sm font-medium hover:bg-zinc-600 transition-colors border border-white/10">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>

            @if(request()->hasAny(['action', 'model_type']))
                <a href="{{ route('admin.activity-log.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-800 text-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-700 transition-colors border border-white/10">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Log Table --}}
    <div class="overflow-x-auto rounded-2xl border border-white/10 bg-zinc-900/60">
        <table class="min-w-full text-sm">
            <thead class="bg-zinc-900">
                <tr class="text-left text-xs uppercase text-zinc-400">
                    <th class="px-4 py-3 w-40">When</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3 w-24">Action</th>
                    <th class="px-4 py-3 w-28">Type</th>
                    <th class="px-4 py-3">Subject</th>
                    <th class="px-4 py-3">Changes</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr class="border-t border-white/5 hover:bg-white/5">
                    <td class="px-4 py-3 text-zinc-400 text-xs whitespace-nowrap">
                        {{ $log->created_at->diffForHumans() }}
                    </td>
                    <td class="px-4 py-3 text-zinc-300">
                        {{ $log->user?->email ?? 'System' }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $actionColors = [
                                'created' => 'bg-emerald-400/20 text-emerald-300',
                                'updated' => 'bg-amber-400/20 text-amber-300',
                                'deleted' => 'bg-red-400/20 text-red-300',
                            ];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $actionColors[$log->action] ?? 'bg-zinc-800 text-zinc-400' }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-zinc-400">
                        {{ $log->model_label }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $subject = $log->subject();
                            $subjectName = $subject?->name ?? "#{$log->model_id}";
                        @endphp
                        <span class="text-white">{{ $subjectName }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($log->changes)
                            <div class="space-y-1">
                                @foreach($log->changes as $field => $diff)
                                    <div class="text-xs">
                                        <span class="text-zinc-500">{{ $field }}:</span>
                                        <span class="text-red-400/70 line-through">{{ \Illuminate\Support\Str::limit($diff['old'] ?? '—', 30) }}</span>
                                        <span class="text-zinc-600">&rarr;</span>
                                        <span class="text-emerald-400/70">{{ \Illuminate\Support\Str::limit($diff['new'] ?? '—', 30) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-zinc-600">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-center text-zinc-400" colspan="6">
                        No activity logged yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
@endsection
