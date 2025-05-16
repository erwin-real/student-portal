<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button, buttonVariants } from '@/components/ui/button';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { capitalize } from 'vue';
import { Separator } from '@/components/ui/separator';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: `${props.user.member.first_name} ${props.user.member.last_name}`,
        href: '/users',
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
                        <CardTitle>User info</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">First Name</div>
                            <div>{{ user.member.first_name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Middle Name</div>
                            <div>{{ user.member.middle_name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Last Name</div>
                            <div>{{ user.member.last_name }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Gender</div>
                            <div>{{ user.member.gender }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Address</div>
                            <div>{{ user.member.address }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Date of birth</div>
                            <div>{{ user.member.date_of_birth }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Email Address</div>
                            <div>{{ user.member.email }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Mobile No.</div>
                            <div>{{ user.member.mobile_no }}</div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Username</div>
                            <div>{{ user.username }}</div>
                        </div>

                        <Separator class="my-4" />

                        <div class="text-base font-semibold">Selected grade levels and sections to this faculty</div>

                        <table v-if="user.member.faculty && user.member.faculty.level_sections.length > 0" class="w-full table-auto border mt-4 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-left p-2 border">Grade Levels</th>
                                    <th class="text-left p-2 border">Sections</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in user.member.faculty.level_sections"
                                    :key="item.id"
                                >
                                    <td class="p-2 border">{{ item.level.name }}</td>
                                    <td class="p-2 border">{{ item.section?.name ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </CardContent>

                    <CardContent class="space-y-2">
                        <!-- <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Grade Level</div>
                            <div>{{ capitalize(user.level.name) }}</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-32 font-semibold">Section</div>
                            <div>{{ capitalize(user.section.name) }}</div>
                        </div> -->
                        <div class="flex justify-between items-center space-x-4 mt-6">
                            <Link :href="route('users.index')" :class="buttonVariants({variant: 'outline'})">Back</Link>
                            <div>
                                <Link :href="route('users.edit', user.id)" :class="buttonVariants({variant: 'default'})">Edit</Link>
                                <!-- <Button class="ml-2" variant="destructive">Delete</Button> -->
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
