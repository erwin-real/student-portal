<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { buttonVariants } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Search } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    students: {
        type: Object,
        required: true
    },
    filters:  {
        type: Object,
        required: true
    },
})

const form = reactive({
  search: props.filters.search || '',
})

const searchStudents = debounce(() => {
  router.get(route('students.index'), { search: form.search }, {
    preserveState: true,
    replace: true,
  })
}, 300)

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Students',
        href: '/students',
    },
];

console.log(props.students)

</script>

<template>
    <Head title="Students" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-3">
            <div class="relative w-full max-w-sm items-center">
                <input v-model="form.search" @input="searchStudents" id="search" type="text" placeholder="Search student" class="pl-10 border-1 border-gray-400 focus:border-gray-700 rounded" />
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                <Search class="size-6 text-muted-foreground" />
                </span>
            </div>
        </div>

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <ScrollArea>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Grade Level</TableHead>
                            <TableHead>Section</TableHead>
                            <TableHead>Gender</TableHead>
                            <TableHead>Address</TableHead>
                            <!-- <TableHead class="w-[120px]">Actions</TableHead> -->
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="student in students.data" :key="student.id">
                            <TableCell>
                                <Link :href="route('students.show', student.id)" class="hover:text-blue-900 text-blue-500">
                                    {{ student.member.first_name }} {{ student.member.last_name}}
                                </Link>
                            </TableCell>
                            <TableCell>{{ student.level.name}}</TableCell>
                            <TableCell>{{ student.section.name}}</TableCell>
                            <TableCell>{{ student.member.gender}}</TableCell>
                            <TableCell>{{ student.member.address}}</TableCell>
                            <!-- <TableCell class="space-x-2"> -->
                                <!-- <Link :href="route('students.show', student.id)" :class="buttonVariants({variant: 'secondary'})">Show</Link> -->
                                <!-- <Link :href="route('students.edit', student.id)" :class="buttonVariants({variant: 'default'})">Edit</Link> -->
                                <!-- Show Edit Delete -->
                            <!-- </TableCell> -->
                        </TableRow>
                    </TableBody>
                </Table>
            </ScrollArea>

            <!-- <div class="mt-3 flex-justify-between">
                <Link :href="students.prev_page_url ?? ''"
                    :disabled="!students.prev_page_url"
                    :class="buttonVariants({variant: 'outline'})">
                    Prev
                </Link>
                <Link :href="students.next_page_url ?? ''"
                    :disabled="!students.next_page_url"
                    :class="buttonVariants({variant: 'outline'})">
                    Next
                </Link>
            </div> -->

            <div class="mt-3 flex justify-between align-center gap-2">
                <span>Showing <strong>{{ students.from }} - {{ students.to }}</strong> of <strong>{{students.total}}</strong></span>
                <div class="space-x-2">
                    <Link :href="link.url ?? ''"
                        v-for="(link, index) in students.links"
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
