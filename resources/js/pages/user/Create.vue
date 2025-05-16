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

const showPassword = ref(false)

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

interface Member {
  id: number
  first_name: string
  middle_name: string
  last_name: string
  gender: string
  address: string
  date_of_birth: string
  email: string
  mobile_no: string
}

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
    members: Member[],
    gradeLevels: GradeLevel[]
}>()

const members = props.members;
const searchQuery = ref('')
const showDropdown = ref(false)
const inputError = ref(false)
const gradeLevels = ref(props.gradeLevels)
const selectedGradeLevelId = ref<number | null>(null)
const selectedSectionId = ref<number | null>(null)
const addedItems = ref<AddedItem[]>([])

const form = useForm({
  id: 0,
  first_name: '',
  middle_name: '',
  last_name: '',
  gender: '',
  address: '',
  date_of_birth: '',
  email: '',
  mobile_no: '',
  username: '',
  password: '',
  grade_section_mappings: []
})

const filteredSections = computed(() => {
    const grade = gradeLevels.value.find(g => g.id === selectedGradeLevelId.value)
    return grade ? grade.sections : []
})

const onGradeLevelChange = () => {
  selectedSectionId.value = null
}

const filteredMembers = computed(() =>
  props.members.filter(member =>
    `${member.first_name} ${member.last_name}`.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
)

const selectMember = (member: Member) => {
  form.id = member.id
  form.first_name = member.first_name
  form.middle_name = member.middle_name
  form.last_name = member.last_name
  form.gender = member.gender
  form.address = member.address
  form.date_of_birth = member.date_of_birth
  form.email = member.email
  form.mobile_no = member.mobile_no
  searchQuery.value = `${member.first_name} ${member.last_name}`
  inputError.value = false
  showDropdown.value = false
}

const hideWithDelay = () => {
  setTimeout(() => {
    showDropdown.value = false
  }, 100)
}

const validateMemberSelected = () => {
  const matched = props.members.find(
    (m) => `${m.first_name} ${m.last_name}`.toLowerCase() === searchQuery.value.trim().toLowerCase()
  )
  if (!matched) {
    inputError.value = true
    form.id = 0
    return false
  }
  return true
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
}

const removeItem = (index: number) => {
  addedItems.value.splice(index, 1)
}

const handleSubmit = () => {
    if (!validateMemberSelected() || !form.id) return

    form.grade_section_mappings = addedItems.value.map(item => ({
        grade_level_id: item.gradeLevel.id,
        section_id: item.section?.id ?? null,
    }))

    form.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('User Created Successfully!')
        },
        onError: () => {
            console.warn('Form submission failed')
        }
    })

}

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

                            <!-- FILTERS -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search Name <span class="text-red-600">*</span></label>
                                <Input
                                    v-model="searchQuery"
                                    :class="[
                                        'w-full',
                                        inputError ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''
                                    ]"
                                    placeholder="Search members..."
                                    @focus="showDropdown = true"
                                    @blur="hideWithDelay"
                                    required
                                />

                                <ul
                                    v-if="showDropdown && filteredMembers.length > 0"
                                    class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto"
                                >
                                    <li
                                        v-for="member in filteredMembers"
                                        :key="member.id"
                                        class="cursor-pointer px-4 py-2 hover:bg-gray-100"
                                        @mousedown.prevent="selectMember(member)"
                                        >
                                        {{ member.first_name }} {{ member.last_name }}
                                    </li>
                                </ul>
                                <p v-if="inputError" class="text-sm text-red-600 mt-1">Member not found in the list.</p>
                            </div>

                            <!-- First Name -->
                            <div class="grid w-full gap-2">
                                <Label for="first_name">First Name</Label>
                                <Input id="first_name" v-model="form.first_name" disabled />
                            </div>

                            <!-- Middle Name -->
                            <div class="grid w-full gap-2">
                                <Label for="middle_name">Middle Name</Label>
                                <Input id="middle_name" v-model="form.middle_name" disabled />
                            </div>

                            <!-- Last Name -->
                            <div class="grid w-full gap-2">
                                <Label for="last_name">Last Name</Label>
                                <Input id="last_name" v-model="form.last_name" disabled />
                            </div>

                            <!-- Gender & Date of birth -->
                            <div class="grid grid-cols-2 gap-6">

                                <div class="grid w-full gap-2">
                                    <Label for="gender">Gender</Label>
                                    <Select id="gender" v-model="form.gender" :disabled="true">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select gender"/>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Male">Male</SelectItem>
                                            <SelectItem value="Female">Female</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div class="grid w-full gap-2">
                                    <Label for="date_of_birth">Date of birth</Label>
                                    <Input id="date_of_birth" v-model="form.date_of_birth" type="date" disabled/>
                                </div>

                            </div>

                            <!-- Address -->
                            <div class="grid w-full gap-2">
                                <Label for="address">Address</Label>
                                <Textarea id="address" v-model="form.address" disabled />
                            </div>

                            <!-- Email & Mobile No -->
                            <div class="grid grid-cols-2 gap-6">

                                <div class="grid w-full gap-2">
                                    <Label for="email">Email</Label>
                                    <Input id="email" v-model="form.email" disabled />
                                </div>

                                <div class="grid w-full gap-2">
                                    <Label for="mobile_no">Mobile No</Label>
                                    <Input id="mobile_no" v-model="form.mobile_no" disabled />
                                </div>

                            </div>

                            <Separator class="my-4" />

                            <!-- Username & Password -->
                            <div class="grid grid-cols-2 gap-6">

                                <div class="grid w-full gap-2">
                                    <Label for="username">Username <span class="text-red-600">*</span></Label>
                                    <Input id="username" v-model="form.username" required/>
                                    <!-- <InputError :message="form.errors.username" /> -->
                                </div>

                                <div class="grid w-full gap-2">
                                    <Label for="password">Password <span class="text-red-600">*</span></Label>
                                    <div class="relative">
                                        <input
                                            :type="showPassword ? 'text' : 'password'"
                                            v-model="form.password"
                                            class="border p-2 pr-10 rounded w-full"
                                            required
                                        />
                                        <button
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute right-2 top-2 text-sm text-gray-600"
                                        >
                                        {{ showPassword ? 'Hide' : 'Show' }}
                                        </button>
                                    </div>
                                    <!-- <InputError :message="form.errors.password" /> -->

                                    <!-- <Label for="password">Password</Label>
                                    <Input id="password" v-model="form.password" />
                                    <InputError :message="form.errors.password" /> -->
                                </div>

                            </div>

                            <Separator class="my-4" />

                            <div class="text-lg font-semibold">Pick grade levels and sections and assign to this faculty</div>

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


                            <div class="text-base font-semibold">Selected grade levels and sections to this faculty</div>

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
                                <Link :class="buttonVariants({variant: 'ghost'})" :href="route('users.index')">Cancel</Link>
                                <Button
                                    type="submit"
                                    variant="default"
                                    :disabled="form.processing"
                                    class="cursor-pointer">
                                    {{ form.processing ? 'Submitting...' : 'Submit' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
