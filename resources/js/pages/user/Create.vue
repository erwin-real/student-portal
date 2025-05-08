<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button, buttonVariants } from '@/components/ui/button';
import { capitalize, ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { toast } from 'vue-sonner';
import { Separator } from '@/components/ui/separator';

const props = defineProps({
    members: {
        type: Array,
        required: true
    },
    levels: {
        type: Array,
        required: true
    },
    sections: {
        type: Array,
        required: true
    }
})

const members = props.members;

const form = useForm({
    member_id: '',
    name: '',
    role: '',
    username: '',
    password: '',
})

const showPassword = ref(false)

// const handleSubmit = () => {
//     form.put(route('users.update', user), {
//         preserveScroll: true,
//         onSuccess: () => toast.success('User Updated Successfully!')
//     })
// }

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: 'Create',
        href: '/users/create',
    },
];
</script>

<template>
    <Head title="Create new User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Create User info</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <!-- <input type="hidden" name="_method" value="PUT"> -->
                            <div class="grid w-full gap-2">
                                <Label for="first_name">First Name</Label>
                                <Input id="first_name" v-model="form.first_name" />
                                <InputError :message="form.errors.first_name" />
                            </div>

                            <div class="grid w-full gap-2">
                                <Label for="middle_name">Middle Name</Label>
                                <Input id="middle_name" v-model="form.middle_name" />
                                <InputError :message="form.errors.middle_name" />
                            </div>

                            <div class="grid w-full gap-2">
                                <Label for="last_name">Last Name</Label>
                                <Input id="last_name" v-model="form.last_name" />
                                <InputError :message="form.errors.last_name" />
                            </div>

                            <div class="grid grid-cols-2 gap-6">

                                <div class="grid w-full gap-2">
                                    <Label for="gender">Gender</Label>
                                    <Select id="gender" v-model="form.gender">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select gender"/>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="male">Male</SelectItem>
                                            <SelectItem value="female">Female</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.gender" />
                                </div>

                                <div class="grid w-full gap-2">
                                    <Label for="date_of_birth">Date of birth</Label>
                                    <Input id="date_of_birth" v-model="form.date_of_birth" type="date" />
                                    <InputError :message="form.errors.date_of_birth" />
                                </div>

                            </div>

                            <div class="grid w-full gap-2">
                                <Label for="address">Address</Label>
                                <Textarea id="address" v-model="form.address" />
                                <InputError :message="form.errors.address" />
                            </div>

                            <div class="grid grid-cols-2 gap-6">

                                <div class="grid w-full gap-2">
                                    <Label for="email">Email</Label>
                                    <Input id="email" v-model="form.email" />
                                    <InputError :message="form.errors.email" />
                                </div>

                                <div class="grid w-full gap-2">
                                    <Label for="mobile_no">Mobile No</Label>
                                    <Input id="mobile_no" v-model="form.mobile_no" />
                                    <InputError :message="form.errors.mobile_no" />
                                </div>

                            </div>

                            <Separator class="my-4" />

                            <div class="grid grid-cols-2 gap-6">

                                <div class="grid w-full gap-2">
                                    <Label for="username">Username</Label>
                                    <Input id="username" v-model="form.username" />
                                    <InputError :message="form.errors.username" />
                                </div>

                                <div class="grid w-full gap-2">
                                    <Label for="password">Change Password</Label>
                                    <div class="relative">
                                        <input
                                            :type="showPassword ? 'text' : 'password'"
                                            v-model="form.password"
                                            class="border p-2 pr-10 rounded w-full"
                                        />
                                        <button
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute right-2 top-2 text-sm text-gray-600"
                                        >
                                        {{ showPassword ? 'Hide' : 'Show' }}
                                        </button>
                                    </div>
                                    <InputError :message="form.errors.username" />

                                    <!-- <Label for="password">Password</Label>
                                    <Input id="password" v-model="form.password" />
                                    <InputError :message="form.errors.password" /> -->
                                </div>

                            </div>
                            <div class="flex justify-between items-center">
                                <Button type="submit" variant="default" :disabled="form.processing">Save user info</Button>
                                <Link :class="buttonVariants({variant: 'ghost'})" :href="route('users.index')">Cancel</Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
