<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button, buttonVariants } from '@/components/ui/button';
import { capitalize } from 'vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    student: {
        type: Object,
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

const student = props.student;

const form = useForm({
    first_name: student.member.first_name,
    middle_name: student.member.middle_name,
    last_name: student.member.last_name,
    gender: student.member.gender,
    address: student.member.address,
    date_of_birth: student.member.date_of_birth,
    email: student.member.email,
    mobile_no: student.member.mobile_no,
    level_id: student.level.id,
    section_id: student?.section?.id
})

const handleSubmit = () => {
    form.put(route('students.update', student), {
        preserveScroll: true,
        onSuccess: () => toast.success('Student Updated Successfully!')
    })
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Students',
        href: '/students',
    },
    {
        title: `${props.student.member.first_name} ${props.student.member.last_name}`,
        href: `/students/${props.student.id}`,
    },
    {
        title: 'Edit',
        href: '/students',
    },
];
</script>

<template>
    <Head title="Edit Student Info" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Edit Student info</CardTitle>
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
                                            <SelectItem value="Male">Male</SelectItem>
                                            <SelectItem value="Female">Female</SelectItem>
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

                            <div class="grid grid-cols-2 gap-6">

                                <div class="grid w-full gap-2">
                                    <Label for="level_id">Grade Level</Label>
                                    <Select id="level_id" v-model="form.level_id">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select Grade Level"/>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="level in levels" :key="level.id" :value="level.id">
                                                {{ capitalize(level.name) }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.level_id" />
                                </div>

                                <div class="grid w-full gap-2">
                                    <Label for="section_id">Section</Label>
                                    <Select id="section_id" v-model="form.section_id">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select section"/>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="section in sections" :key="section.id" :value="section.id">{{ capitalize(section.name) }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.section_id" />
                                </div>

                            </div>
                            <div class="flex justify-between items-center">
                                <Link :class="buttonVariants({variant: 'ghost'})" :href="route('students.show', student.id)">Cancel</Link>
                                <Button
                                    class="cursor-pointer"
                                    type="submit"
                                    variant="default"
                                    :disabled="form.processing">
                                    {{ form.processing ? 'Saving...' : 'Save student info' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
