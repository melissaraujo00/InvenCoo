<div class="relative" x-data="notificationsComponent()" x-init="fetchNotifications()" @click.away="closeDropdown()">
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-gray-900 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()"
        type="button"
    >
        <span x-show="unreadCount > 0" x-cloak class="absolute -right-1 -top-1 z-10 min-w-[20px] h-[20px] rounded-full bg-orange-500 border-2 border-white dark:border-gray-900 text-white text-[10px] font-bold flex items-center justify-center px-1 shadow-sm">
            <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
        </span>

        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z" fill="currentColor"/>
        </svg>
    </button>

    <div
        x-show="dropdownOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2 sm:translate-y-0"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2 sm:translate-y-0"
        class="fixed inset-x-4 top-[76px] sm:absolute sm:inset-auto sm:right-0 sm:top-auto sm:mt-4 flex flex-col sm:w-[380px] max-h-[85vh] sm:max-h-[480px] rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-800 z-[9999] overflow-hidden"
    >
        <div class="shrink-0 flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700/50">
            <h5 class="text-lg font-bold text-gray-800 dark:text-white/90">Notificaciones</h5>
            <button @click="closeDropdown()" class="p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-gray-300 transition-colors">
                <svg class="fill-current w-5 h-5" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z" fill="currentColor"/>
                </svg>
            </button>
        </div>

        <ul class="flex-1 min-h-0 overflow-y-auto custom-scrollbar">
            <template x-for="notif in notifications" :key="notif.id">
                <li @click="markAsRead(notif.id)" class="group border-b border-gray-100 last:border-0 dark:border-gray-700/50">
                    <a :href="notif.data.url || '#'" 
                       class="flex gap-3.5 p-4 hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors" 
                       :class="{'bg-brand-50/50 dark:bg-brand-900/10': !notif.read_at}">
                        
                        <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-full" 
                             :class="!notif.read_at ? 'bg-brand-100 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <span class="block text-sm">
                                <span class="font-bold text-gray-800 dark:text-white/90 truncate block" x-text="notif.data.title"></span>
                                <span class="text-gray-600 dark:text-gray-300 mt-0.5 block leading-tight" x-text="notif.data.message"></span>
                            </span>
                            <span class="flex items-center gap-1.5 mt-1.5 text-xs font-medium text-gray-400 dark:text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span x-text="formatDate(notif.created_at)"></span>
                            </span>
                        </div>
                        
                        <div class="shrink-0 flex items-center justify-center pt-1.5">
                            <div x-show="!notif.read_at" class="w-2.5 h-2.5 rounded-full bg-brand-500 shadow-sm"></div>
                        </div>
                    </a>
                </li>
            </template>
            
            <div x-show="notifications.length === 0" class="flex flex-col items-center justify-center py-10 text-center px-4">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No tienes notificaciones pendientes</p>
            </div>
        </ul>

        <div class="shrink-0 p-3 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700/50">
            <button
                @click="markAllAsRead()"
                class="w-full flex justify-center items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Marcar todas como leídas
            </button>
        </div>
    </div>
</div>

<script>
function notificationsComponent() {
    return {
        dropdownOpen: false,
        notifications: [],
        unreadCount: 0,
        async fetchNotifications() {
            try {
                const response = await fetch('{{ route("notifications.fetch") }}');
                const data = await response.json();
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        },
        toggleDropdown() {
            this.dropdownOpen = !this.dropdownOpen;
        },
        closeDropdown() {
            this.dropdownOpen = false;
        },
        async markAsRead(notificationId) {
            try {
                await fetch('{{ url("/notifications/mark-read") }}/' + notificationId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                
                const notif = this.notifications.find(n => n.id === notificationId);
                if (notif && !notif.read_at) {
                    notif.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
                
                if (notif && notif.data.url) {
                    window.location.href = notif.data.url;
                }
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        },
        async markAllAsRead() {
            try {
                await fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                this.notifications.forEach(n => { if (!n.read_at) n.read_at = new Date().toISOString(); });
                this.unreadCount = 0;
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            if (diffMins < 1) return 'Ahora mismo';
            if (diffMins < 60) return `${diffMins} min`;
            if (diffMins < 1440) return `${Math.floor(diffMins / 60)} h`;
            return date.toLocaleDateString();
        }
    }
}
</script>