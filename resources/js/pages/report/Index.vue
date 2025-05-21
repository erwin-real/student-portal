<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { Button, buttonVariants } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Search } from 'lucide-vue-next';
import { capitalize, computed, onMounted, reactive, ref, watch } from 'vue';
import { debounce } from 'lodash';
import Heading from '@/components/Heading.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import axios from 'axios';

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

const reportTypes = ['Detailed Daily Attendance Logs', 'Daily Attendance']

const reportType = ref('')
const fromDate = ref('')
const toDate = ref('')

const dateError = computed(() => {
  if (fromDate.value && toDate.value) {
    if (fromDate.value > toDate.value) {
      return '"From" date cannot be after "To" date.'
    }
    if (toDate.value < fromDate.value) {
      return '"To" date cannot be before "From" date.'
    }
  }
  return ''
})

const previewPdf = async (memberID: string) => {
  const formData = new FormData()
  formData.append('fromDate', fromDate.value)
  formData.append('toDate', toDate.value)
  formData.append('reportType', reportType.value)
  formData.append('memberID', memberID)

  try {
    const endpoint = `/reports/preview-pdf-${reportType.value === 'Daily Attendance' ? 'daily' : 'detailed'}`;

    const response = await axios.post(endpoint, formData, {
      responseType: 'blob',
    })

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = URL.createObjectURL(blob)
    window.open(url, '_blank')
  } catch (error) {
    console.error('Failed to generate PDF', error)
  }
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
            <Label>Filters:</Label>
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

        <div class="max-w-3xl mx-auto p-6 space-y-6">
            <!-- Type of Report -->
            <div class="flex justify-center items-center space-x-4">
                <label for="reportType" class="text-right text-gray-700 font-medium">
                    Type of Report
                </label>
                <Select v-model="reportType">
                    <SelectTrigger class="w-[220px]">
                        <SelectValue placeholder="Select report type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="type in reportTypes" :key="type" :value="type">
                            {{ type }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- Date Range (From & To) -->
            <div class="flex justify-center items-center space-x-6">
                <!-- From Date -->
                <div class="flex items-center space-x-4">
                    <label for="fromDate" class="w-24 text-right text-gray-700 font-medium">
                    From Date
                    </label>
                    <Input
                        id="fromDate"
                        type="date"
                        v-model="fromDate"
                        class="w-[180px]"
                        :class="{
                            'border-red-500': fromDate && toDate && fromDate > toDate,
                        }"
                    />
                </div>

                <!-- To Date -->
                <div class="flex items-center space-x-4">
                    <label for="toDate" class="w-20 text-right text-gray-700 font-medium">
                    To Date
                    </label>
                    <Input
                        id="toDate"
                        type="date"
                        v-model="toDate"
                        class="w-[180px]"
                        :class="{
                            'border-red-500': fromDate && toDate && toDate < fromDate,
                        }"
                    />
                </div>
            </div>

            <!-- Error Message -->
            <div v-if="dateError" class="text-center text-red-600 font-medium">
                {{ dateError }}
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
                            <TableCell>{{ member.first_name }} {{ member.last_name }}</TableCell>
                            <TableCell>{{ member?.user?.role ?? '---' }}</TableCell>
                            <TableCell>{{ member.student?.level?.name ?? '---' }}</TableCell>
                            <TableCell>{{ member.student?.section?.name ?? '---' }}</TableCell>
                            <TableCell class="space-x-2">
                                <!-- <Link :href="route('reports.index', member.id)" :class="buttonVariants({variant: 'secondary'})">Print</Link> -->
                                 <Button
                                    @click="previewPdf(member.linked_member_id)"
                                    :class="buttonVariants({variant: 'secondary'})"
                                    class="cursor-pointer"
                                    :disabled="!!dateError || !reportType"
                                >
                                    Print
                                </Button>
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
