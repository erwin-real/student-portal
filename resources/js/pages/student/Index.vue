<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { buttonVariants } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';

defineProps({
    students: {
        type: Array,
        required: true
    }
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Students',
        href: '/students',
    },
];
</script>

<template>
    <Head title="Students" />

    <AppLayout :breadcrumbs="breadcrumbs">
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
                        <TableRow v-for="student in students" :key="student.id">
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
        </div>
    </AppLayout>
</template>
