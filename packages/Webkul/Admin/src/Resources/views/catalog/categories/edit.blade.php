@extends('admin::layouts.content')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
@endsection

@section('page_title')
    {{ __('admin::app.catalog.categories.edit-title') }}
@stop

@section('content')
    <div class="content">
        <?php $locale = request()->get('locale') ?: app()->getLocale(); ?>

        <form method="POST" action="#" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ route('admin.dashboard.index') }}';"></i>

                        {{ __('admin::app.catalog.categories.edit-title') }}
                    </h1>

                    <div class="control-group">
                        <select class="control" id="locale-switcher" onChange="window.location.href = this.value">
                            @foreach (core()->getAllLocales() as $localeModel)

                                <option value="{{ route('admin.catalog.categories.update', $category->id) . '?locale=' . $localeModel->code }}" {{ ($localeModel->code) == $locale ? 'selected' : '' }}>
                                    {{ $localeModel->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="page-action">
                    <a href="/{{ $category->url_path }}" style="margin-right: 25px; color: #000;">View Category</a>
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('admin::app.catalog.categories.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()
                    <input name="_method" type="hidden" value="PUT">

                    {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.general.before', ['category' => $category]) !!}

                    <accordian :title="'{{ __('admin::app.catalog.categories.general') }}'" :active="true">
                        <div slot="body">

                            {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.general.controls.before', ['category' => $category]) !!}

                            <div class="control-group" :class="[errors.has('{{$locale}}[name]') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('admin::app.catalog.categories.name') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="name" name="{{$locale}}[name]" value="{{ old($locale)['name'] ?? ($category->translate($locale)['name'] ?? '') }}" data-vv-as="&quot;{{ __('admin::app.catalog.categories.name') }}&quot;" v-slugify-target="'slug'"/>
                                <span class="control-error" v-if="errors.has('{{$locale}}[name]')">@{{ errors.first('{!!$locale!!}[name]') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('status') ? 'has-error' : '']">
                                <label for="status" class="required">{{ __('admin::app.catalog.categories.visible-in-menu') }}</label>
                                <select class="control" v-validate="'required'" id="status" name="status" data-vv-as="&quot;{{ __('admin::app.catalog.categories.visible-in-menu') }}&quot;">
                                    <option value="1" {{ $category->status ? 'selected' : '' }}>
                                        {{ __('admin::app.catalog.categories.yes') }}
                                    </option>
                                    <option value="0" {{ $category->status ? '' : 'selected' }}>
                                        {{ __('admin::app.catalog.categories.no') }}
                                    </option>
                                </select>
                                <span class="control-error" v-if="errors.has('status')">@{{ errors.first('status') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('position') ? 'has-error' : '']">
                                <label for="position" class="required">{{ __('admin::app.catalog.categories.position') }}</label>
                                <input type="text" v-validate="'required|numeric'" class="control" id="position" name="position" value="{{ old('position') ?: $category->position }}" data-vv-as="&quot;{{ __('admin::app.catalog.categories.position') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('position')">@{{ errors.first('position') }}</span>
                            </div>

                            {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.general.controls.after', ['category' => $category]) !!}

                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.general.after', ['category' => $category]) !!}


                    {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.description_images.before', ['category' => $category]) !!}

                    <accordian :title="'{{ __('admin::app.catalog.categories.description-and-images') }}'" :active="true">
                        <div slot="body">

                            {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.description_images.controls.before', ['category' => $category]) !!}

                            <div class="control-group" :class="[errors.has('display_mode') ? 'has-error' : '']">
                                <label for="display_mode" class="required">{{ __('admin::app.catalog.categories.display-mode') }}</label>
                                <select class="control" v-validate="'required'" id="display_mode" name="display_mode" data-vv-as="&quot;{{ __('admin::app.catalog.categories.display-mode') }}&quot;">
                                    <option value="products_and_description" {{ $category->display_mode == 'products_and_description' ? 'selected' : '' }}>
                                        {{ __('admin::app.catalog.categories.products-and-description') }}
                                    </option>
                                    <option value="products_only" {{ $category->display_mode == 'products_only' ? 'selected' : '' }}>
                                        {{ __('admin::app.catalog.categories.products-only') }}
                                    </option>
                                    <option value="description_only" {{ $category->display_mode == 'description_only' ? 'selected' : '' }}>
                                        {{ __('admin::app.catalog.categories.description-only') }}
                                    </option>
                                </select>
                                <span class="control-error" v-if="errors.has('display_mode')">@{{ errors.first('display_mode') }}</span>
                            </div>

                            <description></description>

                            <div class="control-group {!! $errors->has('image.*') ? 'has-error' : '' !!}">
                                <label>{{ __('admin::app.catalog.categories.image') }}</label>

                                <image-wrapper :button-label="'{{ __('admin::app.catalog.products.add-image-btn-title') }}'" input-name="image" :multiple="false"  :images='"{{ $category->image_url }}"'></image-wrapper>

                                <span class="control-error" v-if="{!! $errors->has('image.*') !!}">
                                    @foreach ($errors->get('image.*') as $key => $message)
                                        @php echo str_replace($key, 'Image', $message[0]); @endphp
                                    @endforeach
                                </span>

                            </div>

                            {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.description_images.controls.after', ['category' => $category]) !!}

                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.description_images.after', ['category' => $category]) !!}

                    @if ($categories->count())

                        {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.parent_category.before', ['category' => $category]) !!}

                        <accordian :title="'{{ __('admin::app.catalog.categories.parent-category') }}'" :active="true">
                            <div slot="body">

                                {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.parent_category.controls.before', ['category' => $category]) !!}

                                <tree-view value-field="id" name-field="parent_id" input-type="radio" items='@json($categories)' value='@json($category->parent_id)'></tree-view>

                                {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.parent_category.controls.before', ['category' => $category]) !!}

                            </div>
                        </accordian>

                        {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.parent_category.after', ['category' => $category]) !!}

                    @endif

                    <accordian title="Products" :active="true">
                        <select-products-component slot="body"></select-products-component>
                    </accordian>

                    <accordian :title="'{{ __('admin::app.catalog.categories.filterable-attributes') }}'" :active="true">
                        <div slot="body">

                            <?php $selectedaAtributes = old('attributes') ?? $category->filterableAttributes->pluck('id')->toArray() ?>

                            <div class="control-group" :class="[errors.has('attributes[]') ? 'has-error' : '']">
                                <label for="attributes" class="required">{{ __('admin::app.catalog.categories.attributes') }}</label>
                                <select class="control" name="attributes[]" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.catalog.categories.attributes') }}&quot;" multiple>

                                    @foreach ($attributes as $attribute)
                                        <option value="{{ $attribute->id }}" {{ in_array($attribute->id, $selectedaAtributes) ? 'selected' : ''}}>
                                            {{ $attribute->name ? $attribute->name : $attribute->admin_name }}
                                        </option>
                                    @endforeach

                                </select>
                                <span class="control-error" v-if="errors.has('attributes[]')">
                                    @{{ errors.first('attributes[]') }}
                                </span>
                            </div>
                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.seo.before', ['category' => $category]) !!}

                    <accordian :title="'{{ __('admin::app.catalog.categories.seo') }}'" :active="true">
                        <div slot="body">

                            {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.seo.controls.before', ['category' => $category]) !!}

                            <div class="control-group">
                                <label for="meta_title">{{ __('admin::app.catalog.categories.meta_title') }}</label>
                                <input type="text" class="control" id="meta_title" name="{{$locale}}[meta_title]" value="{{ old($locale)['meta_title'] ?? ($category->translate($locale)['meta_title'] ?? '') }}"/>
                            </div>

                            <div class="control-group" :class="[errors.has('{{$locale}}[slug]') ? 'has-error' : '']">
                                <label for="slug" class="required">{{ __('admin::app.catalog.categories.slug') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="slug" name="{{$locale}}[slug]" value="{{ old($locale)['slug'] ?? ($category->translate($locale)['slug'] ?? '') }}" data-vv-as="&quot;{{ __('admin::app.catalog.categories.slug') }}&quot;" v-slugify/>
                                <span class="control-error" v-if="errors.has('{{$locale}}[slug]')">@{{ errors.first('{!!$locale!!}[slug]') }}</span>
                            </div>

                            <div class="control-group">
                                <label for="meta_description">{{ __('admin::app.catalog.categories.meta_description') }}</label>
                                <textarea class="control" id="meta_description" name="{{$locale}}[meta_description]">{{ old($locale)['meta_description'] ?? ($category->translate($locale)['meta_description'] ?? '') }}</textarea>
                            </div>

                            <div class="control-group">
                                <label for="meta_keywords">{{ __('admin::app.catalog.categories.meta_keywords') }}</label>
                                <textarea class="control" id="meta_keywords" name="{{$locale}}[meta_keywords]">{{ old($locale)['meta_keywords'] ?? ($category->translate($locale)['meta_keywords'] ?? '') }}</textarea>
                            </div>

                            {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.seo.controls.after', ['category' => $category]) !!}

                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.catalog.category.edit_form_accordian.seo.after', ['category' => $category]) !!}

                </div>
            </div>

        </form>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>

    <script type="text/x-template" id="description-template">

        <div class="control-group" :class="[errors.has('{{$locale}}[description]') ? 'has-error' : '']">
            <label for="description" :class="isRequired ? 'required' : ''">{{ __('admin::app.catalog.categories.description') }}</label>
            <textarea v-validate="isRequired ? 'required' : ''" class="control" id="description" name="{{$locale}}[description]" data-vv-as="&quot;{{ __('admin::app.catalog.categories.description') }}&quot;">{{ old($locale)['description'] ?? ($category->translate($locale)['description'] ?? '') }}</textarea>
            <span class="control-error" v-if="errors.has('{{$locale}}[description]')">@{{ errors.first('{!!$locale!!}[description]') }}</span>
        </div>

    </script>

    <script>
        $(document).ready(function () {
            tinymce.init({
                selector: 'textarea#description',
                height: 200,
                width: "100%",
                plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',
                toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code | table',
                image_advtab: true
            });
        });

        Vue.component('description', {

            template: '#description-template',

            inject: ['$validator'],

            data: function() {
                return {
                    isRequired: true,
                }
            },

            created: function () {
                var this_this = this;

                $(document).ready(function () {
                    $('#display_mode').on('change', function (e) {
                        if ($('#display_mode').val() != 'products_only') {
                            this_this.isRequired = true;
                        } else {
                            this_this.isRequired = false;
                        }
                    })

                    if ($('#display_mode').val() != 'products_only') {
                        this_this.isRequired = true;
                    } else {
                        this_this.isRequired = false;
                    }
                });
            }
        })
    </script>

    <script type="text/x-template"  id="select-products-template">
        <div v-loading="loading">
            <h4>Add Products</h4>
            <el-autocomplete
                v-model="addProductSearch"
                :fetch-suggestions="querySearch"
                placeholder="Search for a product by name or SKU"
                ref="addProductSearch"
                style="width: 100%"
                @select="handleSelectProduct"
            ></el-autocomplete>
            <br /><br />
            <h4>Current Products</h4>
            <el-table
                :data="tableData.filter(data => !search || data.name.toLowerCase().includes(search.toLowerCase()) || data.sku.toLowerCase().includes(search.toLowerCase()))"
                style="width: 100%">
                <el-table-column
                    label="SKU"
                    width="250"
                    prop="sku">
                </el-table-column>
                <el-table-column
                    label="Name"
                    prop="name">
                </el-table-column>
                <el-table-column
                    align="right">
                    <template slot="header" slot-scope="scope">
                        <el-input
                            v-model="search"
                            size="mini"
                            placeholder="Search for products in this category"/>
                    </template>
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.temporary" size="small" style="margin-right: 20px;">Adding to Category</el-tag>
                        <el-tag v-if="scope.row.toDelete" size="small" type="danger" style="margin-right: 20px;">Removing from Category</el-tag>
                        <el-button
                            size="mini"
                            @click="handleView(scope.$index, scope.row)">
                            View
                        </el-button>
                        <el-button
                            size="mini"
                            v-if="!scope.row.toDelete"
                            @click="handleRemove(scope.$index, scope.row)">
                            Remove From Category
                        </el-button>
                        <el-button
                            v-else
                            size="mini"
                            @click="handleKeep(scope.$index, scope.row)">
                            Keep In Category
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <input v-for="product in products" type="hidden" name="products[]" :value="product.id" />
        </div>
    </script>

    <script>
        Vue.component('select-products-component', {
            template: '#select-products-template',

            data() {
                return {
                    tableData: @json($category->products),
                    search: '',
                    loading: true,
                    addProductSearch: '',
                    productSearchOptions: [],
                }
            },

            computed: {
                products () {
                    return this.tableData.filter((product) => {
                        return product.toDelete === undefined;
                    });
                },
            },

            mounted() {
                this.loadProductOptions();
            },

            methods: {
                handleView(index, row) {
                    window.open('/' + row.url_key, '_blank');
                },

                handleRemove(index, row) {
                    if (row.temporary) {
                        this.tableData.splice(index, 1);
                    } else {
                        this.tableData[index]['toDelete'] = true;
                        this.$set(this.tableData, index, this.tableData[index]);
                    }
                },

                handleKeep(index, row) {
                    this.tableData[index]['toDelete'] = undefined;
                    this.$set(this.tableData, index, this.tableData[index]);
                },

                querySearch(queryString, cb) {
                    var options = this.productSearchOptions;
                    var results = queryString ? options.filter(this.createFilter(queryString)) : options;
                    // call callback function to return suggestions
                    cb(results);
                },

                createFilter(queryString) {
                    return (option) => {
                        if (option && option.name && option.sku) {
                            let skuSearch = option.sku.toLowerCase().indexOf(queryString.toLowerCase()) === 0,
                                nameSearch = option.name.toLowerCase().indexOf(queryString.toLowerCase()) === 0;

                            return (skuSearch || nameSearch);
                        }

                        return false;
                    };
                },

                loadProductOptions() {
                    window.axios.get('/admin/klb/autocomplete', {
                        params: {
                            'model': '\\Webkul\\Product\\Models\\Product',
                            'queryScope': 'autoCompleteData',
                        }
                    }).then((response) => {
                        this.productSearchOptions = response.data;
                        this.loading = false;
                    }).catch((error) => {
                        console.error(error);
                        this.$notify.error({
                            title: 'Error',
                            message: 'Failed to load Products'
                        });
                    });
                },

                handleSelectProduct(product) {
                    product['temporary'] = 'true';
                    this.tableData.push(product);
                    this.addProductSearch = '';
                }
            },
        });
    </script>
@endpush