import EasyMDE from 'easymde'

import '../../css/components/markdown-editor.css'

export default (Alpine) => {
    Alpine.data('markdownEditorFormComponent', ({
        darkMode,
        state,
        statePath,
        tab,
    }) => {
        return {
            editor: null,

            isStateBeingUpdated: false,

            state,

            tab,

            init: function () {
                this.editor = new EasyMDE({
                    element: this.$refs.input,
                    spellChecker: false,
                    status: false,
                    initialValue: this.state,
                    toolbar: ['upload-image', 'preview'],
                    styleSelectedText: false,
                    blockStyles: {
                        italic: '_',
                    },
                    unorderedListStyle: '-',
                    previewClass: `prose w-full h-full max-w-none px-1 bg-white min-h-[150px] ${darkMode ? 'dark:prose-invert dark:bg-gray-700' : ''}`.trim(),
                    minHeight: '150px',
                    uploadImage: true,
                    direction: document.documentElement.dir,
                    imageUploadFunction: (file, onSuccess, onError) => {
                        if (! file) {
                            return
                        }

                        this.$wire.upload(`componentFileAttachments.${statePath}`, file, () => {
                            this.$wire.getComponentFileAttachmentUrl(statePath).then((url) => {
                                onSuccess(url)
                            })
                        }, () => {
                            onError('Failed to upload file.')
                        })
                    },
                })

                if (this.tab === 'preview') {
                    this.editor.togglePreview()
                }

                this.editor.codemirror.on('change', () => {
                    this.isStateBeingUpdated = true
                    this.state = this.editor.value()
                    this.$nextTick(() => this.isStateBeingUpdated = false)
                })

                this.$watch('tab', () => {
                    if ((this.tab === 'preview') && this.editor.isPreviewActive() || (this.tab === 'write') && (! this.editor.isPreviewActive())) {
                        return
                    }

                    this.editor.togglePreview()
                })

                this.$watch('state', () => {
                    if (this.isStateBeingUpdated) {
                        return
                    }

                    this.editor.value(this.state)
                })
            }
        }
    })
}
