<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center gap-3">
        <x-hr.back-button :fallback="route('hr.candidates.index')" />
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Upload Resume</h2>
            <p class="text-sm text-slate-500">
                Add a candidate by uploading their resume.
                <a href="{{ route('hr.candidates.create') }}" wire:navigate
                    class="font-medium text-brand-teal hover:text-brand-teal-dark">Enter details manually instead</a>
            </p>
        </div>
    </div>

    <form wire:submit="processResume">
        <x-hr.section-card>
            {{--
                Both the empty (dropzone) and selected-file states live in the
                DOM at all times; plain JS toggles which one is visible. This
                whole block is wire:ignore'd so the drag/drop and progress
                event listeners (attached once, below) survive — Livewire
                would otherwise tear down and rebuild this subtree whenever
                $resume changes, silently losing those listeners.
            --}}
            <div wire:ignore>
                <label id="resume-dropzone" for="resume-input"
                    class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-12 text-center transition-colors hover:border-brand-teal hover:bg-brand-teal-light/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                    </svg>
                    <p class="mt-3 text-sm text-slate-600">
                        <span class="font-medium text-brand-teal">Click to upload</span> or drag and drop
                    </p>
                    <p class="mt-1 text-xs text-slate-400">PDF, DOC or DOCX up to 5MB</p>
                    <input id="resume-input" type="file" wire:model="resume" name="resume" class="sr-only"
                        accept=".pdf,.doc,.docx">
                </label>

                <div id="resume-progress" class="mt-4 hidden">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Uploading...</span>
                        <span id="resume-progress-percent">0%</span>
                    </div>
                    <div class="mt-1.5 h-2 w-full rounded-full bg-slate-100">
                        <div id="resume-progress-bar" class="h-2 rounded-full bg-brand-teal transition-all"
                            style="width: 0%"></div>
                    </div>
                </div>

                <div id="resume-preview"
                    class="hidden items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                    <div class="flex min-w-0 items-center gap-3 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 shrink-0 text-brand-teal" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <div class="min-w-0">
                            <p id="resume-filename" class="truncate font-medium text-slate-700"></p>
                            <p class="text-xs text-slate-400"><span id="resume-filesize"></span> · <span
                                    id="resume-filetype"></span></p>
                        </div>
                    </div>
                    <button type="button" id="resume-remove" wire:click="removeResume"
                        class="shrink-0 text-sm font-medium text-rose-500 hover:text-rose-600">
                        Remove
                    </button>
                </div>

                <script>
                    (() => {
                        const dropzone = document.getElementById('resume-dropzone');
                        const input = document.getElementById('resume-input');
                        const preview = document.getElementById('resume-preview');
                        const progress = document.getElementById('resume-progress');
                        const progressBar = document.getElementById('resume-progress-bar');
                        const progressPercent = document.getElementById('resume-progress-percent');
                        // The Continue button's disabled/label state is driven entirely by
                        // these upload events rather than Livewire's $resume property: since
                        // wire:model="resume" (no .live) only commits/re-renders on the NEXT
                        // request, gating "disabled" on $resume being truthy would deadlock —
                        // nothing would ever trigger that next request to flip it.
                        //
                        // Looked up lazily (not cached at the top) because the button lives
                        // outside this wire:ignore'd block, further down in the markup — at
                        // the time this script runs, that element doesn't exist in the DOM
                        // yet, so an eager lookup here would permanently capture null.
                        const setContinueState = (state) => {
                            const continueButton = document.getElementById('resume-continue-button');
                            const continueLabel = document.getElementById('resume-continue-label');

                            if (!continueButton || !continueLabel) return;

                            if (state === 'ready') {
                                continueButton.disabled = false;
                                continueLabel.textContent = 'Continue';
                            } else if (state === 'uploading') {
                                continueButton.disabled = true;
                                continueLabel.textContent = 'Uploading...';
                            } else {
                                continueButton.disabled = true;
                                continueLabel.textContent = 'Continue';
                            }
                        };

                        const formatSize = (bytes) => bytes < 1024 * 1024 ?
                            Math.round(bytes / 1024) + ' KB' :
                            (bytes / (1024 * 1024)).toFixed(1) + ' MB';

                        const showPreview = (file) => {
                            document.getElementById('resume-filename').textContent = file.name;
                            document.getElementById('resume-filesize').textContent = formatSize(file.size);
                            document.getElementById('resume-filetype').textContent = (file.name.split('.').pop() || '')
                                .toUpperCase();
                            dropzone.classList.add('hidden');
                            preview.classList.remove('hidden');
                            preview.classList.add('flex');
                        };

                        const showEmpty = () => {
                            dropzone.classList.remove('hidden');
                            preview.classList.add('hidden');
                            preview.classList.remove('flex');
                            progress.classList.add('hidden');
                            progressBar.style.width = '0%';
                            progressPercent.textContent = '0%';
                        };

                        ['dragenter', 'dragover'].forEach((evt) => dropzone.addEventListener(evt, (e) => {
                            e.preventDefault();
                            dropzone.classList.add('border-brand-teal', 'bg-brand-teal-light/40');
                        }));

                        ['dragleave', 'drop'].forEach((evt) => dropzone.addEventListener(evt, (e) => {
                            e.preventDefault();
                            dropzone.classList.remove('border-brand-teal', 'bg-brand-teal-light/40');
                        }));

                        dropzone.addEventListener('drop', (e) => {
                            if (e.dataTransfer.files.length) {
                                input.files = e.dataTransfer.files;
                                input.dispatchEvent(new Event('change', {
                                    bubbles: true
                                }));
                            }
                        });

                        input.addEventListener('change', () => {
                            if (input.files.length) showPreview(input.files[0]);
                        });

                        input.addEventListener('livewire-upload-start', () => {
                            progress.classList.remove('hidden');
                            setContinueState('uploading');
                        });
                        input.addEventListener('livewire-upload-finish', () => {
                            progress.classList.add('hidden');
                            setContinueState('ready');
                        });
                        input.addEventListener('livewire-upload-error', () => {
                            progress.classList.add('hidden');
                            setContinueState('empty');
                        });
                        input.addEventListener('livewire-upload-progress', (e) => {
                            progressBar.style.width = e.detail.progress + '%';
                            progressPercent.textContent = e.detail.progress + '%';
                        });

                        document.getElementById('resume-remove').addEventListener('click', () => {
                            input.value = '';
                            showEmpty();
                            setContinueState('empty');
                        });
                    })();
                </script>
            </div>

            @error('resume')
                <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
            @enderror

            <p class="mt-4 text-xs text-slate-400">
                Uploading only stores the file for now — resume parsing and candidate creation aren't implemented yet
                (see the TODO in
                <code class="rounded bg-slate-100 px-1 py-0.5">ResumeUpload::processResume()</code>).
            </p>
        </x-hr.section-card>

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('hr.candidates.index') }}" wire:navigate
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit" id="resume-continue-button"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-teal px-5 py-2 text-sm font-medium text-white hover:bg-brand-teal-dark disabled:cursor-not-allowed disabled:opacity-60"
                wire:loading.attr="disabled" wire:target="processResume" disabled>
                <span id="resume-continue-label" wire:loading.remove wire:target="processResume">Continue</span>
                <span wire:loading wire:target="processResume">Processing...</span>
            </button>
        </div>
    </form>
</div>
