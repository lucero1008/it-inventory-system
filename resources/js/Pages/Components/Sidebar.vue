<script setup>
import {
ChartPieIcon,
UserGroupIcon,
PrinterIcon,
KeyIcon, ServerIcon,
ComputerDesktopIcon,
CircleStackIcon,
ListBulletIcon ,
PhoneIcon,
WifiIcon,
TvIcon,
ClockIcon
} from '@heroicons/vue/24/solid'
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

defineProps({
    isOpen: {
        type: Boolean,
        default: false
    }
});

const sidebarSection = [
   {
    header: '',
    links: [
      { name: 'Dashboard', href: '/dashboard', component: 'Dashboard', icon: ChartPieIcon, show: true },
      { name: 'Users', href: '/users', component: 'Users', icon: UserGroupIcon, show: page.props.auth.user.usertype === 'Admin' },
      { name: 'Activity Logs', href: '/logs', component: 'Logs', icon: ClockIcon, show: page.props.auth.user.usertype === 'Admin' },
    ]
  },
  {
    header: 'Hardware Assets',
    links: [
      { name: 'Server', href: '#', component: 'Server', icon: ServerIcon, show: true },
      { name: 'Desktop & Laptop', href: '/device', component: 'Device', icon: ComputerDesktopIcon, show: true },
      { name: 'Monitor', href: '/monitor', component: 'Monitor', icon: TvIcon, show: true },
      { name: 'Printer', href: '/printer', component: 'Printer', icon: PrinterIcon, show: true },
    ]
  },
  {
    header: 'Network',
    links: [
      { name: 'Switch & Router', href: '#', component: 'NetworkDevice', icon: CircleStackIcon, show: true },
      { name: 'Access Phone', href: '#', component: 'AccessPoint', icon: WifiIcon, show: true },
      { name: 'IP Phone', href: '#', component: 'NetworkPhone', icon: PhoneIcon, show: true },
    ]
  },
  {
    header: 'Others',
    links: [
      { name: 'Software License', href: '#', component: 'SoftwareLicense', icon: KeyIcon, show: true },
      { name: 'Other / Peripheral', href: '#', component: 'Other', icon: ListBulletIcon, show: true },
    ]
  }
]

const getLinkClass = (componentName) => {
    const isActive = page.component.includes(componentName);
    return isActive ? 'activeLink' : 'defaultLink';
}

const getIconClass = (componentName) => {
    const isActive = page.component.includes(componentName);
    return isActive ? 'activeIconLink' : 'defaultIconLink';
}
</script>
<template>
    <aside class="fixed top-15 lg:top-10 left-0 z-40 w-64 h-full transition-transform font-poppins shadow-md lg:translate-x-0"
           :class="{
               '-translate-x-full': !isOpen,
               'translate-x-0': isOpen
           }"
    >
        <div class="sidebar-content">
            <div v-for="(section, id) in sidebarSection" :key="id" class="mb-4">

                <p class="sidebar-partition-header">
                    {{ section.header }}
                </p>

                <ul class="space-y-2 font-medium">
                    <template v-for="link in section.links" :key="link.name">
                        <li v-if="link.show">
                            <Link :href="link.href" class="group" :class="getLinkClass(link.component)">
                                <component :is="link.icon" :class="getIconClass(link.component)" />
                                <span class="ms-3">{{ link.name }}</span>
                            </Link>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </aside>
</template>
