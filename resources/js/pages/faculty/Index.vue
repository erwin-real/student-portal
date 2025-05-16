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
import Heading from '@/components/Heading.vue';
import { capitalize } from 'vue';
import { Input } from '@/components/ui/input';

const props = defineProps({
    faculties: {
        type: Object,
        required: true
    },
    filters:  {
        type: Object,
        required: true
    }
})

const form = reactive({
  search: props.filters.search || '',
})

const searchFaculties = debounce(() => {
  router.get(route('faculties.index'), { search: form.search }, {
    preserveState: true,
    replace: true,
  })
}, 300)

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Faculties',
        href: '/faculties',
    },
];

</script>

<template>
    <Head title="Faculties" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-3">
            <Heading title="Faculties" description="Manage faculties" />
            <div class="m-3">
                <div class="relative w-full max-w-sm items-center">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-500">
                        <Search class="h-4 w-4" />
                    </span>
                    <Input
                        v-model="form.search"
                        placeholder="Search faculty"
                        class="pl-10"
                        @input="searchFaculties"
                    />
                </div>
            </div>
        </div>

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <ScrollArea>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Date of birth</TableHead>
                            <TableHead>Gender</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Mobile No</TableHead>
                            <TableHead>Address</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="faculty in faculties.data" :key="faculty.id">
                            <TableCell>
                                <Link :href="route('faculties.show', faculty.id)" class="hover:text-blue-900 text-blue-500">
                                    {{ faculty.member.first_name }} {{ faculty.member.last_name}}
                                </Link>
                            </TableCell>
                            <TableCell>{{ faculty.member.date_of_birth}}</TableCell>
                            <TableCell>{{ faculty.member.gender }}</TableCell>
                            <TableCell>{{ faculty.member.email}}</TableCell>
                            <TableCell>{{ faculty.member.mobile_no}}</TableCell>
                            <TableCell>{{ faculty.member.address}}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </ScrollArea>

            <!-- <div class="mt-3 flex-justify-between">
                <Link :href="faculties.prev_page_url ?? ''"
                    :disabled="!faculties.prev_page_url"
                    :class="buttonVariants({variant: 'outline'})">
                    Prev
                </Link>
                <Link :href="faculties.next_page_url ?? ''"
                    :disabled="!faculties.next_page_url"
                    :class="buttonVariants({variant: 'outline'})">
                    Next
                </Link>
            </div> -->

            <div class="mt-3 flex justify-between align-center gap-2">
                <span>Showing <strong>{{ faculties.from }} - {{ faculties.to }}</strong> of <strong>{{faculties.total}}</strong></span>
                <div class="space-x-2">
                    <Link :href="link.url ?? ''"
                        v-for="(link, index) in faculties.links"
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
