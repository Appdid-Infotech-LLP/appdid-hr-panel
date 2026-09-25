<div>
    <x-hr.modal :show="$show" title="Send Email">
        <div class="space-y-5">
            <div>
                <span class="mb-1.5 block text-sm font-medium text-slate-700">Candidate</span>
                <p class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                    {{ $candidateName }}</p>
            </div>

            <x-hr.input name="recipient" type="email" label="Recipient" required />

            <x-hr.input name="subject" label="Subject" required placeholder="e.g. Your interview has been scheduled" />

            <x-hr.textarea name="message" label="Message" required placeholder="Write your message..."
                :rows="6" />
        </div>

        <x-slot:footer>
            <button type="button" wire:click="close"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Cancel
            </button>
            <button type="button" wire:click="sendEmail"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-4 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark disabled:opacity-60"
                wire:loading.attr="disabled" wire:target="sendEmail">
                <span wire:loading.remove wire:target="sendEmail">Send Email</span>
                <span wire:loading wire:target="sendEmail">Sending...</span>
            </button>
        </x-slot:footer>
    </x-hr.modal>
</div>
