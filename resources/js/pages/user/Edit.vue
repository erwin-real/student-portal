<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Button, buttonVariants } from '@/components/ui/button';
import { capitalize, computed, ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { toast } from 'vue-sonner';
import { Separator } from '@/components/ui/separator';

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

interface AddedItem {
  gradeLevel: GradeLevel
  section: Section
}

const props = defineProps<{
    user: any,
    gradeLevels: GradeLevel[],
    existingItems: []
}>()

const user = props.user;

const gradeLevels = ref(props.gradeLevels)
const selectedGradeLevelId = ref<number | null>(null)
const selectedSectionId = ref<number | null>(null)
const addedItems = ref<AddedItem[]>(props.existingItems)
const showPassword = ref(false)

const form = useForm({
    first_name: user.member.first_name,
    middle_name: user.member.middle_name,
    last_name: user.member.last_name,
    gender: user.member.gender,
    address: user.member.address,
    date_of_birth: user.member.date_of_birth,
    email: user.member.email,
    mobile_no: user.member.mobile_no,
    username: user.username,
    password: user.password,
    grade_section_mappings: []
})

const filteredSections = computed(() => {
    const grade = gradeLevels.value.find(g => g.id === selectedGradeLevelId.value)
    return grade ? grade.sections : []
})

const onGradeLevelChange = () => {
  selectedSectionId.value = null
}

const addItem = () => {
  const grade = gradeLevels.value.find(g => g.id === selectedGradeLevelId.value)
  const section = filteredSections.value.find(s => s.id === selectedSectionId.value) || null

  if (!grade) return

  const isGeneralEntry = section === null

  if (isGeneralEntry) {
    // Remove all existing entries for the same grade level
    addedItems.value = addedItems.value.filter(item => item.gradeLevel.id !== grade.id)
    // Add the grade level with no section
    addedItems.value.push({ gradeLevel: grade, section: null })
  } else {
    const generalExists = addedItems.value.some(
      item => item.gradeLevel.id === grade.id && item.section === null
    )
    if (generalExists) {
      // Do not add specific section if general grade level already exists
      return
    }

    const alreadyExists = addedItems.value.some(
      item => item.gradeLevel.id === grade.id && item.section?.id === section.id
    )
    if (!alreadyExists) {
      addedItems.value.push({ gradeLevel: grade, section })
    }
  }

  console.log(addedItems.value)
  console.log('existingItems', props.existingItems)
}

const removeItem = (index: number) => {
  addedItems.value.splice(index, 1)
}

const handleSubmit = () => {
    form.grade_section_mappings = addedItems.value.map(item => ({
        grade_level_id: item.gradeLevel.id,
        section_id: item.section?.id ?? null,
    }))

    form.put(route('users.update', user), {
        preserveScroll: true,
        onSuccess: () => toast.success('User Updated Successfully!')
    })
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: `${props.user.member.first_name} ${props.user.member.last_name}`,
        href: `/users/${props.user.id}`,
    },
    {
        title: 'Edit',
        href: '/users',
    },
];
</script>

<template>
    <Head title="Edit User Info" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 items-center">
            <div class="flex w-full max-w-2xl flex-col">
                <Card class="mt-3">
                    <CardHeader>
                        <CardTitle>Edit User info</CardTitle>
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

                            <Separator class="my-4" />

                            <div class="text-lg font-sm">Pick grade levels and sections and assign to this faculty</div>

                            <div class="grid grid-cols-5 gap-6">

                                <div class="grid col-span-2 w-full gap-2">
                                    <Label for="levels">Grade Levels</Label>
                                    <Select id="levels" v-model="selectedGradeLevelId" @update:modelValue="onGradeLevelChange">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select Grade Level"/>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="grade in gradeLevels"
                                                :key="grade.id"
                                                :value="grade.id"
                                            >
                                                {{ capitalize(grade.name) }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <!-- <InputError :message="form.errors.level_id" /> -->
                                </div>

                                <div class="grid col-span-2 w-full gap-2">
                                    <Label for="section_id">Section</Label>
                                    <Select id="section_id" v-model="selectedSectionId" :disabled="!filteredSections.length">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select section"/>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="section in filteredSections"
                                                :key="section.id"
                                                :value="section.id"
                                            >
                                                {{ capitalize(section.name) }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <!-- <InputError :message="form.errors.section_id" /> -->
                                </div>

                                <div class=" col-span-1 grid w-full gap-2">
                                    <button
                                        type="button"
                                        class="mt-4 px-4 py-2 rounded-md bg-blue-600 text-white disabled:opacity-50 cursor-pointer"
                                        :disabled="!selectedGradeLevelId"
                                        @click="addItem"
                                    >
                                        Add
                                    </button>
                                </div>

                                </div>

                                <table class="w-full table-auto border mt-4 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-left p-2 border">Grade Levels</th>
                                        <th class="text-left p-2 border">Sections</th>
                                        <th class="text-left p-2 border">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, index) in addedItems"
                                        :key="`${item.gradeLevel.id}-${item.section?.id ?? 'null'}`"
                                    >
                                        <td class="p-2 border">{{ item.gradeLevel.name }}</td>
                                        <td class="p-2 border">{{ item.section?.name ?? '—' }}</td>
                                        <td class="p-2 border">
                                            <button
                                                type="button"
                                                class="text-red-600 hover:underline cursor-pointer"
                                                @click="removeItem(index)"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="addedItems.length === 0">
                                        <td colspan="3" class="text-center p-2 border text-gray-500">
                                            No grade level & section added yet.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="flex justify-between items-center">
                                <Link :class="buttonVariants({variant: 'ghost'})" :href="route('users.show', user.id)">Cancel</Link>
                                <Button
                                    class="cursor-pointer"
                                    type="submit"
                                    variant="default"
                                    :disabled="form.processing">
                                    {{ form.processing ? 'Saving...' : 'Save user info' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
