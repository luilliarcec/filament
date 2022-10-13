<aside
    x-data="{}"
    @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
        x-cloak
        x-bind:class="$store.sidebar.isOpen ? 'filament-sidebar-open translate-x-0 max-w-[20em] lg:max-w-[var(--sidebar-width)]' : '-translate-x-full lg:translate-x-0 lg:max-w-[var(--collapsed-sidebar-width)] rtl:lg:-translate-x-0 rtl:translate-x-full'"
    @else
        x-cloak="-lg"
        x-bind:class="$store.sidebar.isOpen ? 'filament-sidebar-open translate-x-0' : '-translate-x-full lg:translate-x-0 rtl:lg:-translate-x-0 rtl:translate-x-full'"
    @endif
    @class([
        'filament-sidebar fixed inset-y-0 left-0 rtl:left-auto rtl:right-0 z-20 flex flex-col h-screen overflow-hidden shadow-2xl transition-all bg-white lg:border-r rtl:lg:border-r-0 rtl:lg:border-l w-[var(--sidebar-width)] lg:z-0',
        'lg:translate-x-0' => ! config('filament.layout.sidebar.is_collapsible_on_desktop'),
        'dark:bg-gray-800 dark:border-gray-700' => config('filament.dark_mode'),
    ])
>
    <header @class([
        'filament-sidebar-header border-b h-[4rem] shrink-0 flex items-center justify-center relative',
        'dark:border-gray-700' => config('filament.dark_mode'),
    ])>
        <div
            x-cloak
            @class([
                'flex items-center jusify-center px-6 w-full',
                'lg:px-4' => config('filament.layout.sidebar.is_collapsible_on_desktop') && (config('filament.layout.sidebar.collapsed_width') !== 0),
            ])
            x-show="$store.sidebar.isOpen || @js(! config('filament.layout.sidebar.is_collapsible_on_desktop')) || @js(config('filament.layout.sidebar.collapsed_width') === 0)"
        >
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop') && (config('filament.layout.sidebar.collapsed_width') !== 0))
                <button
                    type="button"
                    class="filament-sidebar-collapse-button shrink-0 hidden lg:flex items-center justify-center w-10 h-10 text-primary-500 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none"
                    x-on:click.stop="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                    x-transition:enter="lg:transition delay-100"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                >
                    <svg class="h-6 w-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.25 7.5L16 12L20.25 16.5M3.75 12H12M3.75 17.25H16M3.75 6.75H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            @endif

            <a
                href="{{ config('filament.home_url') }}"
                data-turbo="false"
                @class([
                    'block w-full',
                    'lg:ml-3' => config('filament.layout.sidebar.is_collapsible_on_desktop') && (config('filament.layout.sidebar.collapsed_width') !== 0),
                ])
            >
                <x-filament::brand />
            </a>
        </div>

        @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
            <button
                type="button"
                class="filament-sidebar-close-button shrink-0 flex items-center justify-center w-10 h-10 text-primary-500 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none"
                x-on:click.stop="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                x-show="(! $store.sidebar.isOpen) && @js(config('filament.layout.sidebar.collapsed_width') !== 0)"
                x-transition:enter="lg:transition delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            >
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        @endif
    </header>

    <nav class="flex-1 py-6 overflow-x-hidden overflow-y-auto filament-sidebar-nav">
        <x-filament::layouts.app.sidebar.start />
        {{ \Filament\Facades\Filament::renderHook('sidebar.start') }}

        @php
            $navigation = \Filament\Facades\Filament::getNavigation();

            $collapsedNavigationGroupLabels = collect($navigation)
                ->filter(fn (\Filament\Navigation\NavigationGroup $group): bool => $group->isCollapsed())
                ->map(fn (\Filament\Navigation\NavigationGroup $group): string => $group->getLabel())
                ->values();
        @endphp

        <script>
            if (localStorage.getItem('collapsedGroups') === null) {
                localStorage.setItem('collapsedGroups', JSON.stringify(@js($collapsedNavigationGroupLabels)))
            }
        </script>

        <ul class="px-6 space-y-6">
            @foreach ($navigation as $group)
                <x-filament::layouts.app.sidebar.group :label="$group->getLabel()" :icon="$group->getIcon()" :collapsible="$group->isCollapsible()">
                    @foreach($group->getSubgroups() as $subgroup)
                        <x-filament::layouts.app.sidebar.subgroup
                            :name="$group->getLabel().'.'.$subgroup->getLabel()"
                            :label="$subgroup->getLabel()"
                            :icon="$subgroup->getIcon()"
                        >
                            @foreach ($subgroup->getItems() as $item)
                                <x-filament::layouts.app.sidebar.item
                                    :active="$item->isActive()"
                                    :icon="$item->getIcon()"
                                    :url="$item->getUrl()"
                                    :badge="$item->getBadge()"
                                    :badgeColor="$item->getBadgeColor()"
                                    :shouldOpenUrlInNewTab="$item->shouldOpenUrlInNewTab()"
                                >
                                    {{ $item->getLabel() }}
                                </x-filament::layouts.app.sidebar.item>
                            @endforeach
                        </x-filament::layouts.app.sidebar.subgroup>
                    @endforeach

                    @foreach ($group->getItems() as $item)
                        <x-filament::layouts.app.sidebar.item
                            :active="$item->isActive()"
                            :icon="$item->getIcon()"
                            :url="$item->getUrl()"
                            :badge="$item->getBadge()"
                            :badgeColor="$item->getBadgeColor()"
                            :shouldOpenUrlInNewTab="$item->shouldOpenUrlInNewTab()"
                        >
                            {{ $item->getLabel() }}
                        </x-filament::layouts.app.sidebar.item>
                    @endforeach
                </x-filament::layouts.app.sidebar.group>

                @if (! $loop->last)
                    <li>
                        <div @class([
                            'border-t -mr-6 rtl:-mr-auto rtl:-ml-6',
                            'dark:border-gray-700' => config('filament.dark_mode'),
                        ])></div>
                    </li>
                @endif
            @endforeach
        </ul>

        <x-filament::layouts.app.sidebar.end />
        {{ \Filament\Facades\Filament::renderHook('sidebar.end') }}
    </nav>

    <x-filament::layouts.app.sidebar.footer />
</aside>
