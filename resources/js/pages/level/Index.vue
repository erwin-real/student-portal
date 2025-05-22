<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { buttonVariants } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Plus, Search } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';
import { debounce } from 'lodash';
import Heading from '@/components/Heading.vue';
import { Input } from '@/components/ui/input';

const props = defineProps({
    sections: {
        type: Object,
        required: true
    },
    // filters:  {
    //     type: Object,
    //     required: true
    // }
})

// const form = reactive({
//   search: props.filters.search || ''
// })

// const searchLevels = debounce(() => {
//   router.get(route('levels.index'), { search: form.search }, {
//     preserveState: true,
//     replace: true,
//   })
// }, 300)

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sections',
        href: '/levels',
    },
];

</script>

<template>
    <Head title="Levels" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-3">
            <Heading title="Sections" description="Manage sections" />
            <div class="m-3 flex justify-between align-center gap-2">

                <!-- <div class="relative w-full max-w-sm items-center">

                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-500">
                        <Search class="h-4 w-4" />
                    </span>
                    <Input
                        v-model="form.search"
                        placeholder="Search level"
                        class="pl-10"
                        @input="searchLevels"
                    />

                </div> -->

                <Link :href="route('levels.create')" :class="buttonVariants({variant: 'default'})">
                    <component :is="Plus" />
                    Create new section
                </Link>

            </div>
        </div>

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <ScrollArea>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Grade Levels</TableHead>
                            <TableHead>Sections</TableHead>
                            <TableHead>Description</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="sections.data.length > 0" v-for="section in sections.data" :key="section.id">
                            <TableCell>{{ section.level.name }}</TableCell>
                            <TableCell>{{ section.name }}</TableCell>
                            <TableCell>{{ section.description ?? '---' }}</TableCell>
                        </TableRow>
                        <!-- <template v-for="level in levels.data" :key="level.id">
                            <TableRow v-if="level.sections.length > 0" v-for="section in level.sections" :key="section.id">
                                <TableCell>{{ level.name }}</TableCell>
                                <TableCell>{{ section?.name?.trim() ? section.name : '---' }}</TableCell>
                                <TableCell>{{ section.description ?? '---' }}</TableCell>
                            </TableRow>
                            <TableRow v-else>
                                <TableCell>{{ level.name}}</TableCell>
                                <TableCell class="border px-4 py-2 text-gray-400 italic">—</TableCell>
                                <TableCell class="border px-4 py-2 text-gray-400 italic">—</TableCell>
                            </TableRow>
                        </template> -->
                    </TableBody>
                </Table>
            </ScrollArea>

            <div v-if="sections.data.length > 10" class="mt-3 flex justify-between align-center gap-2">
                <!-- <span>Showing <strong>{{ levels.from }} - {{ levels.to }}</strong> of <strong>{{levels.total}}</strong></span> -->
                <div class="space-x-2">
                    <Link :href="link.url ?? ''"
                        v-for="(link, index) in sections.links"
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
