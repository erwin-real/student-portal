<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button, buttonVariants } from '@/components/ui/button';
import { capitalize, ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    gradeLevels: {
        type: Array,
        required: true
    }
})

const form = useForm({
    level_id: '',
    section_name: '',
    // section_code: '',
    section_description: '',
})

const handleSubmit = () => {
    form.post(route('levels.store'), {
        preserveScroll: true,
        onSuccess: () => toast.success('Section Created Successfully!')
    })
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sections',
        href: '/levels',
    },
    {
        title: 'Create',
        href: '/levels/create',
    },
];
</script>

<template>
    <Head title="Create new section" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Create new section</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <form @submit.prevent="handleSubmit" class="space-y-6">

                            <div class="grid gap-2">
                                <Label for="level_id">Grade Level</Label>
                                <Select id="level_id" v-model="form.level_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Grade Level"/>
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="level in gradeLevels" :key="level.id" :value="level.id">
                                            {{ capitalize(level.name) }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.level_id" />
                            </div>

                            <div class="grid w-full gap-2">
                                <Label for="section_name">Section Name</Label>
                                <Input id="section_name" v-model="form.section_name" />
                                <InputError :message="form.errors.section_name" />
                            </div>

                            <!-- <div class="grid w-full gap-2">
                                <Label for="section_code">Section Code</Label>
                                <Input id="section_code" v-model="form.section_code" />
                                <InputError :message="form.errors.section_code" />
                            </div> -->

                            <div class="grid w-full gap-2">
                                <Label for="section_description">Section Description</Label>
                                <Input id="section_description" v-model="form.section_description" />
                                <InputError :message="form.errors.section_description" />
                            </div>

                            <div class="flex justify-between items-center">
                                <Link :class="buttonVariants({variant: 'ghost'})" :href="route('levels.index')">Cancel</Link>
                                <Button
                                    type="submit"
                                    variant="default"
                                    :disabled="form.processing">
                                    {{ form.processing ? 'Saving...' : 'Save section' }}
                                </Button>

                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
