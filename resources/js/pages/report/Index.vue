<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { buttonVariants } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Search } from 'lucide-vue-next';
import { capitalize, computed, onMounted, reactive, ref, watch } from 'vue';
import { debounce } from 'lodash';
import Heading from '@/components/Heading.vue';
import { Input } from '@/components/ui/input';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';

interface Section {
    id: number
    name: string
    level_id: number
}

interface GradeLevel {
    id: number
    name: string
    sections: Section[]
}

const props = defineProps<{
    members: any
    gradeLevels: GradeLevel[]
    filters: any,
    filteredSections: Section[]
}>()

const gradeLevels = ref<GradeLevel[]>(props.gradeLevels)

const form = reactive({
  search: props.filters.search || '',
  grade_level: props.filters.grade_level || '',
  section_id: props.filters.section_id || '',
})

const applyFilters = () => {
    router.get(route('reports.index'), { ...form }, {
    preserveState: true,
    replace: true,
  })
}

const searchMembers = debounce(applyFilters, 300)

const onGradeLevelChange = () => {
    form.section_id = null
    if (form.grade_level === 'all') form.grade_level = null
}

const onSectionChange = () => {
    if (form.section_id === 'all') form.section_id = null
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: '/reports',
    },
];

</script>

<template>
    <Head title="Reports" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-3">
            <Heading title="Reports" description="Manage reports" />
            <div class="m-3 space-x-3">
                <div class="flex flex-col md:flex-row gap-4 items-center w-full">
                    <!-- Search Input with Icon -->
                    <div class="relative w-full md:w-1/3">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-500">
                            <Search class="h-4 w-4" />
                        </span>
                        <Input
                            v-model="form.search"
                            placeholder="Search by name..."
                            class="pl-10"
                            @input="searchMembers"
                        />
                    </div>

                    <!-- Grade Level Select -->
                    <Select
                        v-model="form.grade_level"
                        @vue:updated="applyFilters"
                        @update:modelValue="onGradeLevelChange"
                    >
                        <SelectTrigger class="w-full md:w-1/3" place>
                            <SelectValue placeholder="Select Grade Level" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Grade Levels</SelectItem>
                            <SelectItem
                                v-for="level in gradeLevels"
                                :key="level.id"
                                :value="level.id"
                            >
                                {{ level.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <!-- Section Select -->
                    <Select
                        v-model="form.section_id"
                        :disabled="!props.filteredSections.length"
                        @vue:updated="applyFilters"
                        @update:model-value="onSectionChange"
                    >
                        <SelectTrigger class="w-full md:w-1/3">
                            <SelectValue placeholder="Select Section" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Sections</SelectItem>
                            <SelectItem
                                v-for="sec in props.filteredSections"
                                :key="sec.id"
                                :value="sec.id"
                            >
                                {{ sec.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

            </div>
        </div>

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <ScrollArea>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Student/Faculty</TableHead>
                            <TableHead>Grade Level</TableHead>
                            <TableHead>Section</TableHead>
                            <TableHead class="w-[120px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="member in members.data" :key="member.id">
                            <TableCell>{{ member.first_name }} {{ member.last_name}}</TableCell>
                            <TableCell>{{  member.faculty ? 'faculty' : 'student'}}</TableCell>
                            <TableCell>{{ member.student?.level?.name ?? '---'}}</TableCell>
                            <TableCell>{{ member.student?.section?.name ?? '---'}}</TableCell>
                            <TableCell class="space-x-2">
                                <Link :href="route('reports.index', member.id)" :class="buttonVariants({variant: 'secondary'})">Print</Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </ScrollArea>

            <div class="mt-3 flex justify-between align-center gap-2">
                <span>Showing <strong>{{ members.from }} - {{ members.to }}</strong> of <strong>{{members.total}}</strong></span>
                <div class="space-x-2">
                    <Link :href="link.url ?? ''"
                        v-for="(link, index) in members.links"
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
