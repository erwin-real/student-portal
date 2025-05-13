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

const props = defineProps<{
    members: Member[],
    levels: [],
    sections: []
}>()

const members = props.members;

const searchQuery = ref('')
const showDropdown = ref(false)
const inputError = ref(false)
const submitting = ref(false)

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
  password: ''
})

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

const handleSubmit = () => {
    // console.log(userForm)
    // userForm.post(route('users.store'), {
    //     preserveScroll: true,
    //     onSuccess: () => toast.success('User Created Successfully!')
    // })

  if (!validateMemberSelected() || !form.id) return

    form.post(route('users.store'), {
    preserveScroll: true,
    onSuccess: () => {
        // Reset or show success message if needed
        toast.success('User Created Successfully!')
        // console.log('Form submitted successfully')
    },
    onError: () => {
        // Errors will already be in `form.errors`
        console.warn('Form submission failed')
    }
    })

}

console.log(members)
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

                            <div class="grid w-full gap-2">
                                <Label for="first_name">First Name</Label>
                                <Input id="first_name" v-model="form.first_name" disabled />
                            </div>

                            <div class="grid w-full gap-2">
                                <Label for="middle_name">Middle Name</Label>
                                <Input id="middle_name" v-model="form.middle_name" disabled />
                            </div>

                            <div class="grid w-full gap-2">
                                <Label for="last_name">Last Name</Label>
                                <Input id="last_name" v-model="form.last_name" disabled />
                            </div>

                            <div class="grid grid-cols-2 gap-6">

                                <div class="grid w-full gap-2">
                                    <Label for="gender">Gender</Label>
                                    <Select id="gender" v-model="form.gender" :disabled="true">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select gender"/>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="male">Male</SelectItem>
                                            <SelectItem value="female">Female</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div class="grid w-full gap-2">
                                    <Label for="date_of_birth">Date of birth</Label>
                                    <Input id="date_of_birth" v-model="form.date_of_birth" type="date" disabled/>
                                </div>

                            </div>

                            <div class="grid w-full gap-2">
                                <Label for="address">Address</Label>
                                <Textarea id="address" v-model="form.address" disabled />
                            </div>

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
                            <div class="flex justify-between items-center">
                                <Link :class="buttonVariants({variant: 'ghost'})" :href="route('users.index')">Cancel</Link>
                                <Button
                                    type="submit"
                                    variant="default"
                                    :disabled="form.processing">
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
