<div class="space-y-6">
    <div class="flex items-center gap-3">
        <x-hr.back-button :fallback="route('hr.candidates.show', $candidateId)" />
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Edit Candidate</h2>
            <p class="text-sm text-slate-500">Update {{ $firstName }} {{ $lastName }}'s details.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <x-hr.section-card title="Personal Details">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                <x-hr.input name="firstName" label="First Name" required />
                <x-hr.input name="lastName" label="Last Name" required />
                <x-hr.input name="email" type="email" label="Email" required />
                <x-hr.input name="phone" label="Phone" required />
                <x-hr.datepicker name="dateOfBirth" label="Date of Birth" :value="$dateOfBirth" :max-date="now()->subYears(16)->format('Y-m-d')" />
                <x-hr.select name="gender" label="Gender" :options="$this->genders()" placeholder="Select gender" />
                <x-hr.select2 name="location" label="Current Location" :options="$this->locations()" :value="$location" placeholder="Select location" />
            </div>

            <div class="mt-5">
                <x-hr.textarea name="address" label="Address" :rows="2" />
            </div>
        </x-hr.section-card>

        <x-hr.section-card title="Professional Details">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                <x-hr.select name="highestQualification" label="Highest Qualification" :options="$this->qualifications()" placeholder="Select qualification" />
                <x-hr.input name="college" label="College / University" />
                <x-hr.input name="experienceYears" type="number" label="Years of Experience" />
                <x-hr.input name="currentCompany" label="Current Company" />
                <x-hr.input name="currentDesignation" label="Current Designation" />
                <div></div>
                <x-hr.input name="currentSalary" label="Current Salary (₹ per annum)" />
                <x-hr.input name="expectedSalary" label="Expected Salary (₹ per annum)" />
                <x-hr.select name="noticePeriod" label="Notice Period" :options="$this->noticePeriods()" placeholder="Select notice period" />
            </div>

            <div class="mt-5">
                <x-hr.select2
                    name="skills" label="Skills" :options="$this->skillSuggestions()" :value="$skills"
                    placeholder="Type to add skills" multiple tags
                />
            </div>
        </x-hr.section-card>

        <x-hr.section-card title="Call Follow-up" subtitle="Captured after HR speaks with the candidate">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                <x-hr.datepicker name="contactedOn" label="Contacted On" :value="$contactedOn" :max-date="now()->format('Y-m-d')" />
                <x-hr.datepicker name="expectedJoiningDate" label="Expected Joining Date (if selected)" :value="$expectedJoiningDate" />
            </div>

            <div class="mt-5">
                <x-hr.select2
                    name="techStack" label="Tech Stack They're Currently Working On" :options="$this->skillSuggestions()" :value="$techStack"
                    placeholder="Type to add technologies" multiple tags
                />
            </div>

            <div class="mt-5">
                <x-hr.textarea name="reasonForLeaving" label="Reason for Leaving Current Organization" placeholder="What did the candidate say?" :rows="2" />
            </div>

            <label class="mt-5 flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" wire:model="agreedToBond" class="rounded border-slate-300 text-brand-teal focus:ring-brand-teal/30">
                Candidate agreed to sign the bond
            </label>
        </x-hr.section-card>

        <x-hr.section-card title="Links & Resume">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                <x-hr.input name="linkedinUrl" type="url" label="LinkedIn Profile" />
                <x-hr.input name="portfolioUrl" type="url" label="Portfolio URL" />
            </div>

            <div class="mt-5">
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Resume</label>

                @if ($resume)
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="flex items-center gap-3 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <span class="font-medium text-slate-700">{{ $resume->getClientOriginalName() }}</span>
                            <span class="text-slate-400">(new file, {{ number_format($resume->getSize() / 1024, 0) }} KB)</span>
                        </div>
                        <button type="button" wire:click="removeResume" class="text-sm font-medium text-rose-500 hover:text-rose-600">Remove</button>
                    </div>
                @elseif ($existingResumeFilename)
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="flex items-center gap-3 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <span class="font-medium text-slate-700">{{ $existingResumeFilename }}</span>
                            <span class="text-slate-400">(current)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <label for="resume" class="cursor-pointer text-sm font-medium text-brand-teal hover:text-brand-teal-dark">Replace</label>
                            <button type="button" wire:click="removeExistingResume" class="text-sm font-medium text-rose-500 hover:text-rose-600">Remove</button>
                        </div>
                        <input id="resume" type="file" wire:model="resume" class="sr-only" accept=".pdf,.doc,.docx">
                    </div>
                @else
                    <label for="resume" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center hover:border-brand-teal hover:bg-brand-teal-light/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                        </svg>
                        <p class="mt-2 text-sm text-slate-600"><span class="font-medium text-brand-teal">Click to upload</span> or drag and drop</p>
                        <p class="mt-1 text-xs text-slate-400">PDF, DOC or DOCX up to 5MB</p>
                        <input id="resume" type="file" wire:model="resume" class="sr-only" accept=".pdf,.doc,.docx">
                    </label>
                @endif

                <div wire:loading wire:target="resume" class="mt-2 text-xs text-slate-500">Uploading...</div>
                @error('resume')
                    <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </x-hr.section-card>

        <x-hr.section-card title="Notes">
            <x-hr.textarea name="notes" :rows="3" />
        </x-hr.section-card>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('hr.candidates.show', $candidateId) }}" wire:navigate class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-5 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark disabled:opacity-60" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
