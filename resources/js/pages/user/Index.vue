<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { buttonVariants } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Search, Plus } from 'lucide-vue-next';
import { capitalize, computed, reactive, ref, watch } from 'vue';
import { debounce } from 'lodash';
import Heading from '@/components/Heading.vue';

const props = defineProps({
    users: {
        type: Object,
        required: true
    },
    filters:  {
        type: Object,
        required: true
    },
    gradeLevels:  {
        type: Array,
        required: true
    },
    sections:  {
        type: Array,
        required: true
    }
})

const form = reactive({
  search: props.filters.search || '',
  grade_level: props.filters.grade_level || '',
  section_id: props.filters.section_id || '',
})

const searchUsers = debounce(() => {
  router.get(route('users.index'), { search: form.search }, {
    preserveState: true,
    replace: true,
  })
}, 300)

function applyFilters() {
  router.get(route('users.index'), { ...form }, {
    preserveState: true,
    replace: true,
  })
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

console.log(props.users)

</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-3">
            <Heading title="Users" description="Manage users" />
            <div class="m-3 flex justify-between align-center gap-2">

                <div class="relative w-full max-w-sm items-center">
                    <input v-model="form.search" @input="searchUsers" id="search" type="text" placeholder="Search user" class="p-1 pl-10 border-1 border-gray-400 focus:border-gray-700 rounded" />
                    <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                        <Search class="size-6 text-muted-foreground" />
                    </span>
                </div>

                <Link :href="route('users.create')" :class="buttonVariants({variant: 'default'})">
                    <component :is="Plus" />
                    Create new user
                </Link>

            </div>

        </div>

        <!-- <div class="ml-3 mb-3 space-x-3">
            <select v-model="form.grade_level" @change="applyFilters" class="border p-2 rounded">
                <option value="">All Grades</option>
                <option v-for="grade in gradeLevels" :key="grade.id" :value="grade.id">
                {{ grade.name }}
                </option>
            </select>

            <select v-model="form.section_id" @change="applyFilters" class="border p-2 rounded">
                <option value="">All Sections</option>
                <option v-for="section in sections" :key="section.id" :value="section.id">
                {{ section.name }}
                </option>
            </select>
        </div> -->

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <ScrollArea>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Role</TableHead>
                            <TableHead>Username</TableHead>
                            <TableHead>Date of birth</TableHead>
                            <TableHead>Gender</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Mobile No</TableHead>
                            <!-- <TableHead>Address</TableHead> -->
                            <!-- <TableHead class="w-[120px]">Actions</TableHead> -->
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell>
                                <Link :href="route('users.show', user.id)" class="hover:text-blue-900 text-blue-500">
                                    {{ user.member.first_name }} {{ user.member.last_name}}
                                </Link>
                            </TableCell>
                            <TableCell>{{ user.role}}</TableCell>
                            <TableCell>{{ user.username}}</TableCell>
                            <TableCell>{{ user.member.date_of_birth}}</TableCell>
                            <TableCell>{{ capitalize(user.member.gender) }}</TableCell>
                            <TableCell>{{ user.member.email}}</TableCell>
                            <TableCell>{{ user.member.mobile_no}}</TableCell>
                            <!-- <TableCell>{{ user.member.address}}</TableCell> -->
                            <!-- <TableCell class="space-x-2"> -->
                                <!-- <Link :href="route('users.show', user.id)" :class="buttonVariants({variant: 'secondary'})">Show</Link> -->
                                <!-- <Link :href="route('users.edit', user.id)" :class="buttonVariants({variant: 'default'})">Edit</Link> -->
                                <!-- Show Edit Delete -->
                            <!-- </TableCell> -->
                        </TableRow>
                    </TableBody>
                </Table>
            </ScrollArea>

            <!-- <div class="mt-3 flex-justify-between">
                <Link :href="users.prev_page_url ?? ''"
                    :disabled="!users.prev_page_url"
                    :class="buttonVariants({variant: 'outline'})">
                    Prev
                </Link>
                <Link :href="users.next_page_url ?? ''"
                    :disabled="!users.next_page_url"
                    :class="buttonVariants({variant: 'outline'})">
                    Next
                </Link>
            </div> -->

            <div class="mt-3 flex justify-between align-center gap-2">
                <span>Showing <strong>{{ users.from }} - {{ users.to }}</strong> of <strong>{{users.total}}</strong></span>
                <div class="space-x-2">
                    <Link :href="link.url ?? ''"
                        v-for="(link, index) in users.links"
                        :key="index"
                        v-html="link.label"
                        :class="[
                            buttonVariants({variant: link.active ? 'default' : 'outline'}),
                            !link.url ? 'pointer-events-none opacity-50' : ''
                        ]">
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
