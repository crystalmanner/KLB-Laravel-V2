<template>
    <div>
        <div class="image-wrapper">
            <image-item
                v-for='(image, index) in items'
                :key='image.id'
                :image="image"
                :input-name="inputName"
                :required="required"
                :remove-button-label="removeButtonLabel"
                @onRemoveImage="removeImage($event)"
            ></image-item>
        </div>

        <label class="btn btn-lg btn-primary" style="display: inline-block; width: auto" @click="createFileType">{{ buttonLabel }}</label>
    </div>
</template>

<script>
    export default {
        props: {
            buttonLabel: {
                type: String,
                required: false,
                default: 'Add Image'
            },

            removeButtonLabel: {
                type: String,
                required: false,
                default: 'Remove Image'
            },

            inputName: {
                type: String,
                required: false,
                default: 'attachments'
            },

            images: {
                type: Array|String,
                required: false,
                default: () => ([])
            },

            multiple: {
                type: Boolean,
                required: false,
                default: true
            },

            required: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        data: function() {
            return {
                imageCount: 0,
                items: []
            }
        },

        created () {
            var this_this = this;

            if(this.multiple) {
                if (this.images.length) {
                    this.images.forEach(function(image) {
                        this_this.items.push(image)

                        this_this.imageCount++;
                    });
                } else if (this.images.length == undefined && typeof this.images == 'object') {
                    var images = Object.keys(this.images).map(key => { 
                        return this.images[key]; 
                    }); 

                    images.forEach(function(image) {
                        this_this.items.push(image)

                        this_this.imageCount++;
                    });
                } else {
                    this.createFileType();
                }
            } else {
                if(this.images && this.images != '') {
                    this.items.push({'id': 'image_' + this.imageCount, 'url': this.images})

                    this.imageCount++;
                } else {
                    this.createFileType();
                }
            }
        },

        methods: {
            createFileType () {
                var this_this = this;

                if(!this.multiple) {
                    this.items.forEach(function(image) {
                        this_this.removeImage(image)
                    });
                }

                this.imageCount++;

                this.items.push({'id': 'image_' + this.imageCount});
            },

            removeImage (image) {
                let index = this.items.indexOf(image)

                Vue.delete(this.items, index);
            }
        }

    }
</script>
