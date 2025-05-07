<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { capitalize } from 'vue';

const props = defineProps({
    student: {
        type: Object,
        required: true
    }
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Students',
        href: '/students',
    },
    {
        title: `${props.student.member.first_name} ${props.student.member.last_name}`,
        href: '/students',
    },
];
</script>

<template>
    <Head title="Basic Info" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Student info</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">First Name</div>
                            <div>{{ student.member.first_name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Middle Name</div>
                            <div>{{ student.member.middle_name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Last Name</div>
                            <div>{{ student.member.last_name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Gender</div>
                            <div>{{ capitalize(student.member.gender) }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Address</div>
                            <div>{{ student.member.address }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Date of birth</div>
                            <div>{{ student.member.date_of_birth }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Email Address</div>
                            <div>{{ student.member.email }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Mobile No.</div>
                            <div>{{ student.member.mobile_no }}</div>
                        </div>
                    </CardContent>
                    <CardContent class="space-y-2">
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Grade Level</div>
                            <div>{{ capitalize(student.level.name) }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Section</div>
                            <div>{{ capitalize(student.section.name) }}</div>
                        </div>
                        <div class="flex justify-between items-center space-x-4 mt-6">
                            <Link :href="route('students.index')" :class="buttonVariants({variant: 'outline'})">Back</Link>
                            <div>
                                <Link :href="route('students.edit', student.id)" :class="buttonVariants({variant: 'default'})">Edit</Link>
                                <!-- <Button class="ml-2" variant="destructive">Delete</Button> -->
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
