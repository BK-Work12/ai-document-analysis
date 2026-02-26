@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus-visible:border-indigo-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/25 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500']) }}>

