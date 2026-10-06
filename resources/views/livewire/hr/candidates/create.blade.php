<div class="space-y-6">
    <div class="flex items-center gap-3">
        <x-hr.back-button :fallback="route('hr.candidates.index')" />
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Add New Candidate</h2>
            <p class="text-sm text-slate-500">Upload a resume to fill in the details automatically, or enter them manually.</p>
        </div>
    </div>

    <form wire:submit="save" class="relative space-y-6">
        {{-- Covers the whole form (and blocks clicks) while the resume uploads
             and is read; wire:target="resume" stays active for both requests.
             The spinner is sticky so it stays in view on this long form. --}}
        <div wire:loading wire:target="resume" class="absolute inset-0 z-30 rounded-xl bg-white/70 backdrop-blur-[1px]">
            <div class="sticky top-[40vh] flex flex-col items-center gap-3 py-6">
                <svg class="h-8 w-8 animate-spin text-brand-teal" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                </svg>
                <p class="text-sm font-medium text-slate-700">Reading resume and filling in details...</p>
            </div>
        </div>

        <x-hr.section-card title="Resume" subtitle="PDF, DOC or DOCX up to 5MB — details are read as soon as it uploads">
            @if ($resume)
                <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="flex min-w-0 items-center gap-3 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="truncate font-medium text-slate-700">{{ $resume->getClientOriginalName() }}</span>
                        <span class="shrink-0 text-slate-400">({{ number_format($resume->getSize() / 1024, 0) }} KB)</span>
                    </div>
                    <button type="button" wire:click="removeResume" class="shrink-0 text-sm font-medium text-rose-500 hover:text-rose-600">Remove</button>
                </div>
            @else
                {{-- The file input covers the whole box (invisible), so clicking
                     and native drag-and-drop both work without any JS. --}}
                <div class="relative flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center hover:border-brand-teal hover:bg-brand-teal-light/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                    </svg>
                    <p class="mt-2 text-sm text-slate-600"><span class="font-medium text-brand-teal">Click to upload</span> or drag and drop</p>
                    <input id="resume" type="file" wire:model="resume" class="absolute inset-0 h-full w-full cursor-pointer opacity-0" accept=".pdf,.doc,.docx">
                </div>
            @endif

            @error('resume')
                <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
            @enderror

            @if ($resumeParseStatus === 'filled')
                <p class="mt-3 text-sm text-emerald-600">Details were filled in from the resume — please review them before saving.</p>
            @elseif ($resumeParseStatus === 'failed')
                <p class="mt-3 text-sm text-amber-600">The resume was attached, but we couldn't read details from it. Please fill the form in manually.</p>
            @endif
        </x-hr.section-card>

        <x-hr.section-card title="Personal Details">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                <x-hr.input name="firstName" label="First Name" required placeholder="Rahul" />
                <x-hr.input name="lastName" label="Last Name" required placeholder="Sharma" />
                <x-hr.input name="email" type="email" label="Email" required placeholder="rahul.sharma@example.com" />
                <x-hr.input name="phone" label="Phone" required placeholder="+91 98765 43210" />
                <x-hr.datepicker name="dateOfBirth" label="Date of Birth" :value="$dateOfBirth" :max-date="now()->subYears(16)->format('Y-m-d')" />
                <x-hr.select2 name="gender" label="Gender" :options="$this->genders()" :value="$gender" placeholder="Select gender" />
                <x-hr.select2 name="location" label="Current Location" :options="$this->locations()" :value="$location" placeholder="Select location" />
            </div>

            <div class="mt-5">
                <x-hr.textarea name="address" label="Address" placeholder="Full address" :rows="2" />
            </div>
        </x-hr.section-card>

        <x-hr.section-card title="Professional Details">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                <div>
                    <x-hr.select2 name="highestQualification" label="Highest Qualification" :options="$this->qualifications()" :value="$highestQualification" placeholder="Select qualification" />

                    @if ($highestQualification === 'Other')
                        <div class="mt-2">
                            <x-hr.input name="highestQualificationOther" placeholder="Enter qualification" />
                        </div>
                    @endif
                </div>
                <x-hr.input name="college" label="College / University" placeholder="RV College of Engineering" />
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Experience</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-hr.input name="experienceYears" type="number" min="0" placeholder="0" />
                            <p class="mt-1 text-xs text-slate-400">Years</p>
                        </div>
                        <div>
                            <x-hr.input name="experienceMonths" type="number" min="0" max="11" placeholder="0" />
                            <p class="mt-1 text-xs text-slate-400">Months</p>
                        </div>
                    </div>
                </div>
                <x-hr.input name="currentCompany" label="Current Company" placeholder="TechNova Solutions" />
                <x-hr.input name="currentDesignation" label="Current Designation" placeholder="Senior Frontend Developer" />
            </div>

            <div class="mt-5">
                <x-hr.select2
                    name="skills" label="Skills" :options="$this->skillSuggestions()" :value="$skills"
                    placeholder="Type to add skills" multiple tags
                />
                <p class="mt-1.5 text-xs text-slate-400">Pick from the list or type a skill and press enter to add it.</p>
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

        <x-hr.section-card title="Compensation & Notice Period">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                <x-hr.input name="currentSalary" label="Current Salary (₹ per annum)" placeholder="e.g. 14,00,000" />
                <x-hr.input name="expectedSalary" label="Expected Salary (₹ per annum)" placeholder="e.g. 20,00,000" />
                <x-hr.select2 name="noticePeriod" label="Notice Period" :options="$this->noticePeriods()" :value="$noticePeriod" placeholder="Select notice period" />
            </div>
        </x-hr.section-card>

        <x-hr.section-card title="Links">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                <x-hr.input name="linkedinUrl" type="url" label="LinkedIn Profile" placeholder="https://linkedin.com/in/..." />
                <x-hr.input name="portfolioUrl" type="url" label="Portfolio URL" placeholder="https://..." />
            </div>
        </x-hr.section-card>

        <x-hr.section-card title="Notes">
            <x-hr.textarea name="notes" placeholder="Any additional notes about this candidate..." :rows="3" />
        </x-hr.section-card>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('hr.candidates.index') }}" wire:navigate class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-5 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark disabled:opacity-60" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Save Candidate</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
