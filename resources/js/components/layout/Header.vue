<template>
    <header class="z-10" :class="{ dark: store.semidark && store.menu === 'horizontal' }">
        <div class="shadow-sm">
            <div class="relative bg-white flex w-full items-center px-5 py-2.5 dark:bg-[#0e1726]">
                <div class="horizontal-logo flex lg:hidden justify-between items-center ltr:mr-2 rtl:ml-2">
                    <router-link to="/" class="main-logo flex items-center shrink-0">
                        <img class="w-8 ltr:-ml-1 rtl:-mr-1 inline" :src="appLogo" alt="" />
                        <span
                            class="text-2xl ltr:ml-1.5 rtl:mr-1.5 font-semibold align-middle hidden md:inline dark:text-white-light transition-all duration-300">EFC-AJK</span>
                    </router-link>

                    <a href="javascript:;"
                       class="collapse-icon flex-none dark:text-[#d0d2d6] hover:text-primary dark:hover:text-primary flex lg:hidden ltr:ml-2 rtl:mr-2 p-2 rounded-full bg-white-light/40 dark:bg-dark/40 hover:bg-white-light/90 dark:hover:bg-dark/60"
                       @click="store.toggleSidebar()">
                        <icon-menu class="w-5 h-5" />
                    </a>
                </div>
                <div
                    class="sm:flex-1 ltr:sm:ml-0 ltr:ml-auto sm:rtl:mr-0 rtl:mr-auto flex items-center space-x-1.5 lg:space-x-2 rtl:space-x-reverse dark:text-[#d0d2d6] relative">
                    <div class="sm:ltr:mr-auto sm:rtl:ml-auto">
                        <form
                            class="sm:relative absolute inset-x-0 sm:top-0 top-1/2 sm:translate-y-0 -translate-y-1/2 sm:mx-0 mx-4 z-10 sm:block hidden"
                            :class="{ '!block': search }" @submit.prevent="search = false">
                        </form>

                        <button type="button"
                                class="search_btn sm:hidden p-2 rounded-full bg-white-light/40 dark:bg-dark/40 hover:bg-white-light/90 dark:hover:bg-dark/60"
                                @click="search = !search">
                            <icon-search class="w-4.5 h-4.5 mx-auto dark:text-[#d0d2d6]" />
                        </button>
                    </div>
                    <div class="absolute left-1/2 -translate-x-1/2">
                        <h3 class="font-bold">{{ store.user ? 'Welcome, ' + store.user?.name : '' }}</h3>
                    </div>

                    <div class="flex justify-center items-center">
                        <template v-if="store.monitoring">
                            <IconOnline class="cursor-pointer" v-if="store?.isOnline" @click="toggleNetworkMonitoring"
                                        title="Network is being monitored" />
                            <IconOffline class="cursor-pointer" v-else @click="toggleNetworkMonitoring"
                                         title="Network is disconnected" />
                        </template>
                        <template v-else>
                            <IconMonitoringStopped class="cursor-pointer" @click="toggleNetworkMonitoring"
                                                   title="Monitoring of network/server connectivity is stopped, click to start it." />
                        </template>
                    </div>

                    <div class="dropdown shrink-0">
                        <Popper :placement="store.rtlClass === 'rtl' ? 'bottom-end' : 'bottom-start'" offsetDistance="8"
                                class="align-middle">
                            <button type="button"
                                    class="relative block p-2 rounded-full bg-white-light/40 dark:bg-dark/40 hover:text-primary hover:bg-white-light/90 dark:hover:bg-dark/60">
                                <icon-bell-bing />

                                <span class="flex absolute w-3 h-3 ltr:right-0 rtl:left-0 top-0">
                                    <span
                                        class="animate-ping absolute ltr:-left-[3px] rtl:-right-[3px] -top-[3px] inline-flex h-full w-full rounded-full bg-success/50 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full w-[6px] h-[6px] bg-success"></span>
                                </span>
                            </button>
                            <template #content="{ close }">
                                <ul
                                    class="!py-0 text-dark dark:text-white-dark w-[300px] sm:w-[350px] divide-y dark:divide-white/10">
                                    <li>
                                        <div class="flex items-center px-4 py-2 justify-between font-semibold">
                                            <h4 class="text-lg">Notification</h4>
                                            <template v-if="notifications.length">
                                                <span class="badge bg-primary/80"
                                                      v-text="notifications.length + 'New'"></span>
                                            </template>
                                        </div>
                                    </li>
                                    <template v-for="notification in notifications" :key="notification.id">
                                        <li class="dark:text-white-light/90">
                                            <div class="group flex items-center px-4 py-2">
                                                <div class="grid place-content-center rounded">
                                                    <div class="w-12 h-12 relative">
                                                        <img class="w-12 h-12 rounded-full object-cover"
                                                             :src="`/assets/images/${notification.profile}`" alt="" />
                                                        <span
                                                            class="bg-success w-2 h-2 rounded-full block absolute right-[6px] bottom-0"></span>
                                                    </div>
                                                </div>
                                                <div class="ltr:pl-3 rtl:pr-3 flex flex-auto">
                                                    <div class="ltr:pr-3 rtl:pl-3">
                                                        <h6 v-html="notification.message"></h6>
                                                        <span class="text-xs block font-normal dark:text-gray-500"
                                                              v-text="notification.time"></span>
                                                    </div>
                                                    <button type="button"
                                                            class="ltr:ml-auto rtl:mr-auto text-neutral-300 hover:text-danger opacity-0 group-hover:opacity-100"
                                                            @click="removeNotification(notification.id)">
                                                        <icon-x-circle />
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    </template>
                                    <template v-if="notifications.length">
                                        <li>
                                            <div class="p-4">
                                                <button class="btn btn-primary block w-full btn-small"
                                                        @click="close()">Read All Notifications
                                                </button>
                                            </div>
                                        </li>
                                    </template>
                                    <template v-if="!notifications.length">
                                        <li>
                                            <div
                                                class="!grid place-content-center hover:!bg-transparent text-lg min-h-[200px]">
                                                <div
                                                    class="mx-auto ring-4 ring-primary/30 rounded-full mb-4 text-primary">
                                                    <icon-info-circle :fill="true" class="w-10 h-10" />
                                                </div>
                                                No data available.
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </Popper>
                    </div>

                    <div class="dropdown shrink-0">
                        <Popper :placement="store.rtlClass === 'rtl' ? 'bottom-end' : 'bottom-start'" offsetDistance="8"
                                class="!block align-middle">
                            <button type="button" class="relative group block">
                                <img class="w-9 h-9 rounded-full object-cover saturate-50 group-hover:saturate-100"
                                     :src="userProfileImage" alt="User Profile" />
                            </button>
                            <template #content="{ close }">
                                <ul
                                    class="text-dark dark:text-white-dark !py-0 w-[230px] font-semibold dark:text-white-light/90">
                                    <li>
                                        <div class="flex items-center px-4 py-4">
                                            <div class="flex-none">
                                                <img class="rounded-md w-10 h-10 object-cover"
                                                     :src="userProfileImage" alt="User Profile" />
                                            </div>
                                            <div class="ltr:pl-4 rtl:pr-4 truncate">
                                                <h5 class="text-base">
                                                    {{ store.user?.name }}
                                                </h5>
                                                <p>
                                                    <span
                                                        class="text-sm bg-success-light rounded text-success px-1 ltr:ml-2 rtl:ml-2">
                                                        {{ store.user?.roles?.[0]?.name }}
                                                    </span>
                                                </p>

                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <router-link to="/profile" class="dark:hover:text-white" @click="close()">
                                            <icon-user class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2 shrink-0" />
                                            Profile
                                        </router-link>
                                    </li>
                                    <li>
                                        <router-link to="/enable-two-factor" class="dark:hover:text-white" @click="close()">
                                            <icon-lock-dots class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2 shrink-0" />
                                            2F Authentication
                                        </router-link>
                                    </li>
                                    <li>
                                        <router-link to="/change-password" class="dark:hover:text-white" @click="close()">
                                            <icon-lock-dots class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2 shrink-0" />
                                            Change Password
                                        </router-link>
                                    </li>
                                    <li class="border-t border-white-light dark:border-white-light/10">
                                        <button :disabled="isLoggingOut" class="w-full text-left dark:hover:text-white" @click="logout()">
                                            <icon-logout class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2 rotate-90 shrink-0" />
                                            Log Out
                                        </button>
                                    </li>
                                </ul>
                            </template>
                        </Popper>
                    </div>
                </div>
            </div>

            <!-- horizontal menu -->
            <ul
                class="horizontal-menu hidden py-1.5 font-semibold px-6 lg:space-x-1.5 xl:space-x-8 rtl:space-x-reverse bg-white border-t border-[#ebedf2] dark:border-[#191e3a] dark:bg-[#0e1726] text-black dark:text-white-dark">
                <li class="menu nav-item relative">
                    <a href="javascript:;" class="nav-link">
                        <div class="flex items-center">
                            <icon-menu-dashboard class="shrink-0" />

                            <span class="px-2">{{ $t('dashboard') }}</span>
                        </div>
                        <div class="right_arrow">
                            <icon-caret-down />
                        </div>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <router-link to="/">{{ $t('sales') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/analytics">{{ $t('analytics') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/finance">{{ $t('finance') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/crypto">{{ $t('crypto') }}</router-link>
                        </li>
                    </ul>
                </li>
                <li class="menu nav-item relative">
                    <a href="javascript:;" class="nav-link">
                        <div class="flex items-center">
                            <icon-menu-apps class="shrink-0" />

                            <span class="px-2">{{ $t('apps') }}</span>
                        </div>
                        <div class="right_arrow">
                            <icon-caret-down />
                        </div>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <router-link to="/apps/chat">{{ $t('chat') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/apps/mailbox">{{ $t('mailbox') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/apps/todolist">{{ $t('todo_list') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/apps/notes">{{ $t('notes') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/apps/scrumboard">{{ $t('scrumboard') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/apps/contacts">{{ $t('contacts') }}</router-link>
                        </li>
                        <li class="relative">
                            <a href="javascript:;">{{ $t('invoice') }}
                                <div class="ltr:ml-auto rtl:mr-auto rtl:rotate-90 -rotate-90">
                                    <icon-caret-down />
                                </div>
                            </a>
                            <ul
                                class="rounded absolute top-0 ltr:left-[95%] rtl:right-[95%] min-w-[180px] bg-white z-[10] text-dark dark:text-white-dark dark:bg-[#1b2e4b] shadow p-0 py-2 hidden">
                                <li>
                                    <router-link to="/apps/invoice/list">{{ $t('list') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/apps/invoice/preview">{{ $t('preview') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/apps/invoice/add">{{ $t('add') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/apps/invoice/edit">{{ $t('edit') }}</router-link>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <router-link to="/apps/calendar">{{ $t('calendar') }}</router-link>
                        </li>
                    </ul>
                </li>
                <li class="menu nav-item relative">
                    <a href="javascript:;" class="nav-link">
                        <div class="flex items-center">
                            <icon-menu-components class="shrink-0" />
                            <span class="px-2">{{ $t('components') }}</span>
                        </div>
                        <div class="right_arrow">
                            <icon-caret-down />
                        </div>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <router-link to="/components/tabs">{{ $t('tabs') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/accordions">{{ $t('accordions') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/modals">{{ $t('modals') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/cards">{{ $t('cards') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/carousel">{{ $t('carousel') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/countdown">{{ $t('countdown') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/counter">{{ $t('counter') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/sweetalert">{{ $t('sweet_alerts') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/timeline">{{ $t('timeline') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/notifications">{{ $t('notifications') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/media-object">{{ $t('media_object') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/list-group">{{ $t('list_group') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/pricing-table">{{ $t('pricing_tables') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/components/lightbox">{{ $t('lightbox') }}</router-link>
                        </li>
                    </ul>
                </li>
                <li class="menu nav-item relative">
                    <a href="javascript:;" class="nav-link">
                        <div class="flex items-center">
                            <icon-menu-elements class="shrink-0" />
                            <span class="px-2">{{ $t('elements') }}</span>
                        </div>
                        <div class="right_arrow">
                            <icon-caret-down />
                        </div>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <router-link to="/elements/alerts">{{ $t('alerts') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/avatar">{{ $t('avatar') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/badges">{{ $t('badges') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/breadcrumbs">{{ $t('breadcrumbs') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/buttons">{{ $t('buttons') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/buttons-group">{{ $t('button_groups') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/color-library">{{ $t('color_library') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/dropdown">{{ $t('dropdown') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/infobox">{{ $t('infobox') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/jumbotron">{{ $t('jumbotron') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/loader">{{ $t('loader') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/pagination">{{ $t('pagination') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/popovers">{{ $t('popovers') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/progress-bar">{{ $t('progress_bar') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/search">{{ $t('search') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/tooltips">{{ $t('tooltips') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/treeview">{{ $t('treeview') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/elements/typography">{{ $t('typography') }}</router-link>
                        </li>
                    </ul>
                </li>
                <li class="menu nav-item relative">
                    <a href="javascript:;" class="nav-link">
                        <div class="flex items-center">
                            <icon-menu-datatables class="shrink-0" />
                            <span class="px-2">{{ $t('tables') }}</span>
                        </div>
                        <div class="right_arrow">
                            <icon-caret-down />
                        </div>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <router-link to="/tables">{{ $t('tables') }}</router-link>
                        </li>
                        <li class="relative">
                            <a href="javascript:;">{{ $t('datatables') }}
                                <div class="ltr:ml-auto rtl:mr-auto rtl:rotate-90 -rotate-90">
                                    <icon-caret-down />
                                </div>
                            </a>
                            <ul
                                class="rounded absolute top-0 ltr:left-[95%] rtl:right-[95%] min-w-[180px] bg-white z-[10] text-dark dark:text-white-dark dark:bg-[#1b2e4b] shadow p-0 py-2 hidden">
                                <li>
                                    <router-link to="/datatables/basic">{{ $t('basic') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/advanced">{{ $t('advanced') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/skin">{{ $t('skin') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/order-sorting">{{ $t('order_sorting') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/columns-filter">{{ $t('columns_filter')
                                        }}
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/multi-column">{{ $t('multi_column') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/multiple-tables">{{ $t('multiple_tables')
                                        }}
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/alt-pagination">{{ $t('alt_pagination')
                                        }}
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/checkbox">{{ $t('checkbox') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/range-search">{{ $t('range_search') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/export">{{ $t('export') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/sticky-header">{{ $t('sticky_header') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/clone-header">{{ $t('clone_header') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/datatables/column-chooser">{{ $t('column_chooser')
                                        }}
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="menu nav-item relative">
                    <a href="javascript:;" class="nav-link">
                        <div class="flex items-center">
                            <icon-menu-forms class="shrink-0" />
                            <span class="px-2">{{ $t('forms') }}</span>
                        </div>
                        <div class="right_arrow">
                            <icon-caret-down />
                        </div>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <router-link to="/forms/basic">{{ $t('basic') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/input-group">{{ $t('input_group') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/layouts">{{ $t('layouts') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/validation">{{ $t('validation') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/input-mask">{{ $t('input_mask') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/select2">{{ $t('select2') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/touchspin">{{ $t('touchspin') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/checkbox-radio">{{ $t('checkbox_and_radio') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/switches">{{ $t('switches') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/wizards">{{ $t('wizards') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/file-upload">{{ $t('file_upload') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/quill-editor">{{ $t('quill_editor') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/markdown-editor">{{ $t('markdown_editor') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/date-picker">{{ $t('date_and_range_picker') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/forms/clipboard">{{ $t('clipboard') }}</router-link>
                        </li>
                    </ul>
                </li>
                <li class="menu nav-item relative">
                    <a href="javascript:;" class="nav-link">
                        <div class="flex items-center">
                            <icon-menu-pages class="shrink-0" />
                            <span class="px-2">{{ $t('pages') }}</span>
                        </div>
                        <div class="right_arrow">
                            <icon-caret-down />
                        </div>
                    </a>
                    <ul class="sub-menu">
                        <li class="relative">
                            <a href="javascript:;">{{ $t('users') }}
                                <div class="ltr:ml-auto rtl:mr-auto rtl:rotate-90 -rotate-90">
                                    <icon-caret-down />
                                </div>
                            </a>
                            <ul
                                class="rounded absolute top-0 ltr:left-[95%] rtl:right-[95%] min-w-[180px] bg-white z-[10] text-dark dark:text-white-dark dark:bg-[#1b2e4b] shadow p-0 py-2 hidden">
                                <li>
                                    <router-link to="/users/profile">{{ $t('profile') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/users/user-account-settings">{{ $t('account_settings')
                                        }}
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <router-link to="/pages/knowledge-base">{{ $t('knowledge_base') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/pages/contact-us-boxed" target="_blank">{{ $t('contact_us_boxed')
                                }}
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/pages/contact-us-cover" target="_blank">{{ $t('contact_us_cover')
                                }}
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/pages/faq">FAQ</router-link>
                        </li>
                        <li>
                            <router-link to="/pages/coming-soon-boxed" target="_blank">{{ $t('coming_soon_boxed')
                                }}
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/pages/coming-soon-cover" target="_blank">{{ $t('coming_soon_cover')
                                }}
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/pages/maintenence" target="_blank">{{ $t('maintenence') }}</router-link>
                        </li>
                        <li class="relative">
                            <a href="javascript:;">{{ $t('error') }}
                                <div class="ltr:ml-auto rtl:mr-auto rtl:rotate-90 -rotate-90">
                                    <icon-caret-down />
                                </div>
                            </a>
                            <ul
                                class="rounded absolute top-0 ltr:left-[95%] rtl:right-[95%] min-w-[180px] bg-white z-[10] text-dark dark:text-white-dark dark:bg-[#1b2e4b] shadow p-0 py-2 hidden">
                                <li>
                                    <router-link to="/pages/error404" target="_blank">{{ $t('404') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/pages/error500" target="_blank">{{ $t('500') }}</router-link>
                                </li>
                                <li>
                                    <router-link to="/pages/error503" target="_blank">{{ $t('503') }}</router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="relative">
                            <a href="javascript:;">{{ $t('login') }}
                                <div class="ltr:ml-auto rtl:mr-auto rtl:rotate-90 -rotate-90">
                                    <icon-caret-down />
                                </div>
                            </a>
                            <ul
                                class="rounded absolute top-0 ltr:left-[95%] rtl:right-[95%] min-w-[180px] bg-white z-[10] text-dark dark:text-white-dark dark:bg-[#1b2e4b] shadow p-0 py-2 hidden">
                                <li>
                                    <router-link to="/auth/cover-login" target="_blank">{{ $t('login_cover')
                                        }}
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/auth/boxed-signin" target="_blank">{{ $t('login_boxed')
                                        }}
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="relative">
                            <a href="javascript:;">{{ $t('register') }}
                                <div class="ltr:ml-auto rtl:mr-auto rtl:rotate-90 -rotate-90">
                                    <icon-caret-down />
                                </div>
                            </a>
                            <ul
                                class="rounded absolute top-0 ltr:left-[95%] rtl:right-[95%] min-w-[180px] bg-white z-[10] text-dark dark:text-white-dark dark:bg-[#1b2e4b] shadow p-0 py-2 hidden">
                                <li>
                                    <router-link to="/auth/cover-register" target="_blank">{{ $t('register_cover')
                                        }}
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/auth/boxed-signup" target="_blank">{{ $t('register_boxed')
                                        }}
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="relative">
                            <a href="javascript:;">{{ $t('password_recovery') }}
                                <div class="ltr:ml-auto rtl:mr-auto rtl:rotate-90 -rotate-90">
                                    <icon-caret-down />
                                </div>
                            </a>
                            <ul
                                class="rounded absolute top-0 ltr:left-[95%] rtl:right-[95%] min-w-[180px] bg-white z-[10] text-dark dark:text-white-dark dark:bg-[#1b2e4b] shadow p-0 py-2 hidden">
                                <li>
                                    <router-link to="/auth/cover-password-reset" target="_blank">{{
                                            $t('recover_id_cover')
                                        }}
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/auth/boxed-password-reset" target="_blank">{{
                                            $t('recover_id_boxed')
                                        }}
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="relative">
                            <a href="javascript:;">{{ $t('lockscreen') }}
                                <div class="ltr:ml-auto rtl:mr-auto rtl:rotate-90 -rotate-90">
                                    <icon-caret-down />
                                </div>
                            </a>
                            <ul
                                class="rounded absolute top-0 ltr:left-[95%] rtl:right-[95%] min-w-[180px] bg-white z-[10] text-dark dark:text-white-dark dark:bg-[#1b2e4b] shadow p-0 py-2 hidden">
                                <li>
                                    <router-link to="/auth/cover-lockscreen" target="_blank">{{ $t('unlock_cover')
                                        }}
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/auth/boxed-lockscreen" target="_blank">{{ $t('unlock_boxed')
                                        }}
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="menu nav-item relative">
                    <a href="javascript:;" class="nav-link">
                        <div class="flex items-center">
                            <icon-menu-more class="shrink-0" />

                            <span class="px-2">{{ $t('more') }}</span>
                        </div>
                        <div class="right_arrow">
                            <icon-caret-down />
                        </div>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <router-link to="/dragndrop">{{ $t('drag_and_drop') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/charts">{{ $t('charts') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/font-icons">{{ $t('font_icons') }}</router-link>
                        </li>
                        <li>
                            <router-link to="/widgets">{{ $t('widgets') }}</router-link>
                        </li>
                        <li>
                            <a href="https://vristo.sbthemes.com" target="_blank">{{ $t('documentation') }}</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </header>
</template>

<script lang="ts" setup>
    import { ref, onMounted, computed, reactive, watch, nextTick } from 'vue';
    import { useI18n } from 'vue-i18n';
    import appSetting from '@/app-setting';
    import apiClient from '@/services/axios';
    import { useRoute } from 'vue-router';
    import { useAppStore } from '@/stores';

    import IconMenu from '@/components/icon/icon-menu.vue';
    import IconSearch from '@/components/icon/icon-search.vue';
    import IconXCircle from '@/components/icon/icon-x-circle.vue';
    import IconInfoCircle from '@/components/icon/icon-info-circle.vue';
    import IconBellBing from '@/components/icon/icon-bell-bing.vue';
    import IconUser from '@/components/icon/icon-user.vue';
    import IconLockDots from '@/components/icon/icon-lock-dots.vue';
    import IconLogout from '@/components/icon/icon-logout.vue';
    import IconMenuDashboard from '@/components/icon/menu/icon-menu-dashboard.vue';
    import IconCaretDown from '@/components/icon/icon-caret-down.vue';
    import IconMenuApps from '@/components/icon/menu/icon-menu-apps.vue';
    import IconMenuComponents from '@/components/icon/menu/icon-menu-components.vue';
    import IconMenuElements from '@/components/icon/menu/icon-menu-elements.vue';
    import IconMenuDatatables from '@/components/icon/menu/icon-menu-datatables.vue';
    import IconMenuForms from '@/components/icon/menu/icon-menu-forms.vue';
    import IconMenuPages from '@/components/icon/menu/icon-menu-pages.vue';
    import IconMenuMore from '@/components/icon/menu/icon-menu-more.vue';
    import IconOnline from '@/components/icon/icon-online.vue';
    import IconOffline from '@/components/icon/icon-offline.vue';
    import IconMonitoringStopped from '@/components/icon/icon-monitoring-stopped.vue';
    import router from '@/router';
    import appLogo from '@/assets/images/logo.png';
    const store = useAppStore();
    const route = useRoute();
    const search = ref(false);
    const i18n = reactive(useI18n());
    const isLoggingOut = ref(false);


    const changeLanguage = (item: any) => {
        i18n.locale = item.code;
        appSetting.toggleLanguage(item);
    };
    const currentFlag = computed(() => {
        return `/assets/images/flags/${i18n.locale.toUpperCase()}.svg`;
    });

   const triggerGlobalSearch = () => {
    if (store.globalSearchQuery && store.globalSearchQuery.trim() !== '') {
        router.push({
            path: '/applications/all',
            query: { search: store.globalSearchQuery }
        });
    }
};

watch(() => store.globalSearchQuery, (val) => {
    if (val && val.trim() !== '') {
        router.push({ path: '/applications/all', query: { search: val } });
    }
});


const clearSearch = () => {
    store.globalSearchQuery = '';

    router.replace({ path: '/', query: {} });
};

const userProfileImage = computed(() => {
    const img = (store.user as any)?.image; // cast to any
    return img ? `http://localhost:8000/storage/${img}` : '/assets/images/user-profile.jpeg';
});


    const notifications = ref([
        {
            id: 1,
            profile: 'user-profile.jpeg',
            message: '<strong class="text-sm mr-1">John Doe</strong>invite you to <strong>Prototyping</strong>',
            time: '45 min ago'
        },
        {
            id: 2,
            profile: 'profile-34.jpeg',
            message: '<strong class="text-sm mr-1">Adam Nolan</strong>mentioned you to <strong>UX Basics</strong>',
            time: '9h Ago'
        },
        {
            id: 3,
            profile: 'profile-16.jpeg',
            message: '<strong class="text-sm mr-1">Anna Morgan</strong>Upload a file',
            time: '9h Ago'
        }
    ]);

    const messages = ref([
        {
            id: 1,
            image: '<span class="grid place-content-center w-9 h-9 rounded-full bg-success-light dark:bg-success text-success dark:text-success-light"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg></span>',
            title: 'Congratulations!',
            message: 'Your OS has been updated.',
            time: '1hr'
        },
        {
            id: 2,
            image: '<span class="grid place-content-center w-9 h-9 rounded-full bg-info-light dark:bg-info text-info dark:text-info-light"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></span>',
            title: 'Did you know?',
            message: 'You can switch between artboards.',
            time: '2hr'
        }
    ]);

    onMounted(() => {
        setActiveDropdown();
    });

    watch(route, (to, from) => {
        setActiveDropdown();
    });

    const setActiveDropdown = () => {
        const selector = document.querySelector('ul.horizontal-menu a[href="' + window.location.pathname + '"]');
        if (selector) {
            selector.classList.add('active');
            const all: any = document.querySelectorAll('ul.horizontal-menu .nav-link.active');
            for (let i = 0; i < all.length; i++) {
                all[0]?.classList.remove('active');
            }
            const ul: any = selector.closest('ul.sub-menu');
            if (ul) {
                let ele: any = ul.closest('li.menu').querySelectorAll('.nav-link');
                if (ele) {
                    ele = ele[0];
                    setTimeout(() => {
                        ele?.classList.add('active');
                    });
                }
            }
        }
    };

    const toggleNetworkMonitoring = () => {
        if (store.monitoring) {
            store.stopMonitoring();
        } else {
            store.startMonitoring();
        }
    };

    const removeNotification = (value: number) => {
        notifications.value = notifications.value.filter((d) => d.id !== value);
    };

    const removeMessage = (value: number) => {
        messages.value = messages.value.filter((d) => d.id !== value);
    };

    const logout =  async  () => {
        isLoggingOut.value = true;
        await apiClient.post('/logout');

        isLoggingOut.value = false;
        store.toggleAuthenticated()
        localStorage.removeItem('isAuthenticated');
        router.push('/login');
    };
</script>
