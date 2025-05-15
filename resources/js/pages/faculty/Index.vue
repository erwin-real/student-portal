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

const props = defineProps({
    faculties: {
        type: Object,
        required: true
    },
    filters:  {
        type: Object,
        required: true
    },
    // gradeLevels:  {
    //     type: Array,
    //     required: true
    // },
    // sections:  {
    //     type: Array,
    //     required: true
    // }
})

const form = reactive({
  search: props.filters.search || '',
//   grade_level: props.filters.grade_level || '',
//   section_id: props.filters.section_id || '',
})

const searchFaculties = debounce(() => {
  router.get(route('faculties.index'), { search: form.search }, {
    preserveState: true,
    replace: true,
  })
}, 300)

// function applyFilters() {
//   router.get(route('faculties.index'), { ...form }, {
//     preserveState: true,
//     replace: true,
//   })
// }

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Faculties',
        href: '/faculties',
    },
];

console.log(props.faculties)

</script>

<template>
    <Head title="Faculties" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-3">
            <Heading title="Faculties" description="Manage faculties" />
            <div class="m-3">
                <div class="relative w-full max-w-sm items-center">
                    <input v-model="form.search" @input="searchFaculties" id="search" type="text" placeholder="Search faculty" class="p-1 pl-10 border-1 border-gray-400 focus:border-gray-700 rounded" />
                    <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                        <Search class="size-6 text-muted-foreground" />
                    </span>
                </div>
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
                            <TableHead>Date of birth</TableHead>
                            <TableHead>Gender</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Mobile No</TableHead>
                            <TableHead>Address</TableHead>
                            <!-- <TableHead class="w-[120px]">Actions</TableHead> -->
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

                            <!-- <TableCell class="space-x-2"> -->
                                <!-- <Link :href="route('faculties.show', faculty.id)" :class="buttonVariants({variant: 'secondary'})">Show</Link> -->
                                <!-- <Link :href="route('faculties.edit', faculty.id)" :class="buttonVariants({variant: 'default'})">Edit</Link> -->
                                <!-- Show Edit Delete -->
                            <!-- </TableCell> -->
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
