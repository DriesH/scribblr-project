<template lang="html">
    <div class="sidebar-dries">
        <!-- FORM START -->
        <form class="bottom-buffer" @submit="addQuote($event)">
            <button @click="closeAddQuoteForm"
                type="button"
                class="btn btn-danger pull-right bottom-buffer"
                name="hide" >
                    <i class="fa fa-times"></i>
            </button>

            <!-- ERROR FIELD START -->
            <div v-if="errors.errorQuote"
            class="alert alert-danger"
            role="alert">
                <p>
                    {{ errors.messageQuote }}
                </p>
            </div>

            <div v-if="errors.errorPicture"
            class="alert alert-danger"
            role="alert" >
                <p>
                    {{ errors.messagePicture }}
                </p>
            </div>
            <!-- ERROR FIELD END -->

            <!-- NAME START -->
            <div class="form-group">
                <label for="quoteName">Quote: </label>

                <input v-model="quote.quoteName"
                type="text"
                class="form-control"
                id="quoteName"
                placeholder="Your quote..."
                maxlength="105">

            </div>
            <!-- NAME END -->

            <!-- DEFAULT BACKGROUND SELECTOR START -->
            <h4>Select a default background, or choose a custom one.</h4>
            <div class="cc-selector-2">
                <div v-for="defaultImg in defaultImgs">

                    <input v-model="backgroundChosen"
                    id="{{defaultImg}}"
                    type="radio"
                    name="background"
                    value="{{defaultImgs.indexOf(defaultImg)}}" />

                    <label v-bind:style="{ backgroundImage : 'url(' + prefixDefault + defaultImg + ')' }"
                    class="drinkcard-cc"
                    for="{{defaultImg}}"></label>

                </div>
                <div style="height:80px;">

                    <input v-model="backgroundChosen"
                    id="customBackground"
                    type="radio"
                    name="background"
                    value="custom" />

                    <label style="background-image: url(/pictures/custom-add.png)"
                    class="drinkcard-cc pull-left"
                    for="customBackground" ></label>

                    <p class="pull-left" style="padding-top:25px;">
                        Custom background
                    </p>
                </div>
            </div>
            <!-- DEFAULT BACKGROUND SELECTOR END -->

            <!-- CUSTOM BACKGROUND START -->
            <div v-if="backgroundChosen === 'custom'"
            id="upload_file">

                <div class="form-group">

                    <label for="backgr_img">
                        <h4>Select an image</h4>
                    </label>

                    <input @change="previewBackground"
                    v-model="quote.backgrImg"
                    name="backgr_img"
                    type="file"
                    id="testimage">

                </div>

                <div class="alert alert-info" role="alert">
                    <strong>Heads up!</strong> For best image quality, use an image with a <strong>1:1</strong> aspect ratio (eg. 400x400).
                </div>

            </div>
            <!-- CUSTOM BACKGROUND END -->

            <!-- PREVIEW START -->
            <div v-if="backgroundChosen !== 'custom'"
            class="quote"
            id="widget">

                <img v-bind:src="previewBackgroundIMG = prefixDefault + defaultImgs[backgroundChosen]"
                id="target" />

                <span class="quote_text">
                    <p class="quoteBox">
                        {{ quote.quoteName }}
                    </p>
                </span>

            </div>

            <div v-else>
                <div class="quote grid-item" id="widget">

                    <img :src="previewBackgroundIMG"
                    class="uploaded_img" />

                    <span class="quote_text">
                        <p class="quoteBox">
                            {{ quote.quoteName }}
                        </p>
                    </span>

                </div>
            </div>

            <div id="img-out"></div>
            <!-- PREVIEW END -->

            <!-- BUTTONS START -->
            <button
                type="submit"
                name="add"
                class="btn btn-success">
                    <i class="fa fa-plus"></i>
                    Add Quote</button>

            <button @click="closeAddQuoteForm"
                type="button"
                class="btn btn-danger pull-right"
                name="hide" >
                    <i class="fa fa-ban"></i>
                    Cancel
            </button>

            <!-- BUTTONS END -->
        </form>
        <!-- FORM END -->
    </div>
</template>

<script>
    export default {
        props: {
            addQuoteShow: {
                type: Boolean
            },
            selectedChild: {
                type: [String, Number]
            },
            previousQuotes: {
                type: Array
            },
            sideBarShow: {
                type: Boolean
            }
        },
        data () {
            return {
                quote: {
                    quoteName: '',
                    backgrImg: ''
                },
                errors: {
                    errorQuote: false,
                    messageQuote: 'You have to fill in a quote! Silly you!',
                    errorPicture: false,
                    messagePicture: 'You have to add a picture! Otherwise your quote will be so... empty!'
                },
                previewBackgroundIMG: '',
                backgroundChosen: 0,
                formData: new FormData(),
                defaultImgs: ['wood.jpg', 'chalkboard.jpg', 'dirt.jpg'],
                prefixDefault: '/pictures/'
            }
        },
        ready () {

        },
        watch: {
            backgroundChosen: function(value) {
                if(value === 'custom'){
                    this.previewBackgroundIMG = '';
                }
            }
        },
        methods: {
            closeAddQuoteForm: function () {
                this.addQuoteShow = false;
                this.sideBarShow = true;
            },
            addQuote: function(event) {
                event.preventDefault();
                if(this.quote.quoteName !== '' && this.previewBackgroundIMG !== '') {
                    this.errors.errorQuote = false;
                    this.errors.errorPicture = false;
                    var fileInputEl = $("#testimage");
                    var self = this;
                    html2canvas($("#widget"), {
                        onrendered: function(canvas) {
                            var theCanvas = canvas;
                            document.body.appendChild(canvas);



                            // Convert and download as image
                            $("#img-out").append(canvas);
                            self.canvas = document.getElementsByTagName('canvas')[0].toDataURL();
                            // Clean up
                            //document.body.removeChild(canvas);
                            if(self.backgroundChosen === 'custom') {
                                self.formData.append("backgroundImage", fileInputEl[0].files[0]);  // custom image
                            }
                            else{
                                self.formData.append("backgroundImage", self.defaultImgs[self.backgroundChosen]);
                            }

                            self.formData.append("imgWithQuote", self.canvas);                    // quote + image base64
                            self.formData.append("quote", self.quote.quoteName);                  // quote raw text
                            self.formData.append("child_id", self.selectedChild);                 // child_id of current child you're adding quote

                            self.$http.post('api/quote', self.formData).then((success_response) => {
                                self.closeAddQuoteForm();
                                self.previousQuotes.push(JSON.parse(success_response.body)[0]);
                            },
                            (error_response) => {
                                alert('Something went wrong!');
                            });
                        }
                    });
                }
                else if( this.quote.quoteName === '' &&  this.previewBackgroundIMG === '' ) {
                    this.errors.errorQuote = true;
                    this.errors.errorPicture = true;
                }
                else if(this.quote.quoteName === '' ){
                    this.errors.errorQuote = true;
                    this.errors.errorPicture = false;
                }
                else if(this.previewBackgroundIMG === '' ) {
                    this.errors.errorQuote = false;
                    this.errors.errorPicture = true;
                }
            },
            //preview the backgroun that was uploaded by the user
            previewBackground: function(event) {
                var input = event.target;

                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    var vm = this;

                    reader.onload = function(e) {
                        vm.previewBackgroundIMG = e.target.result;
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            },
            hasClass: function (element, cls) {
                return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
            }
        },
        components: {

        }
    }
</script>

<style lang="css" scoped>
    .cc-selector-2 input{
        position:absolute;
        z-index:999;
    }
    .cc-selector-2 input:active +.drinkcard-cc, .cc-selector input:active +.drinkcard-cc{opacity: .9;}
    .cc-selector-2 input:checked +.drinkcard-cc, .cc-selector input:checked +.drinkcard-cc{
        -webkit-filter: none;
           -moz-filter: none;
                filter: none;
    }
    .drinkcard-cc{
        cursor:pointer;
        background-size:contain;
        background-repeat:no-repeat;
        display:inline-block;
        width:100px;height:70px;
        -webkit-transition: all 100ms ease-in;
           -moz-transition: all 100ms ease-in;
                transition: all 100ms ease-in;
        -webkit-filter: brightness(1.8) grayscale(1) opacity(.7);
           -moz-filter: brightness(1.8) grayscale(1) opacity(.7);
                filter: brightness(1.8) grayscale(1) opacity(.7);
    }
    .drinkcard-cc:hover{
        -webkit-filter: brightness(1.2) grayscale(.5) opacity(.9);
           -moz-filter: brightness(1.2) grayscale(.5) opacity(.9);
                filter: brightness(1.2) grayscale(.5) opacity(.9);
    }
    .quote {
        position: relative;
        overflow: hidden;
    }
    .quote img {
        border-radius: 10px;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .quote_text {
        color: white;
        font-size: 40px;
        font-family: 'Amatic', cursive;
        position: absolute;
        top: 20px;
        left: 10px;
        text-shadow: 2px 2px 0px #000;
        word-break: break-all;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .quote_text:hover{
        cursor: default;
    }
    .uploaded_img {
        width: 400px;
        height: 400px;
    }
    #quoteTextArea {
        resize: none;
        overflow: hidden;
        word-wrap: normal;
    }
    .quoteBox {
        white-space: normal;
        word-break: normal;
    }
    #widget{
        display: block;
        width: 400px;
        height: 400px;
        margin-bottom: 10px;
    }
    #img-out{
        display: none;
        width: 400px;
        height: 400px;
    }
    #img-out>canvas{
        width: 400px;
        height: 400px;
    }
    .bottom-buffer{
        margin-bottom: 25px;
    }

</style>
