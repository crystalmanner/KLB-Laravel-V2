@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.catalog.products.add-title') }}
@stop

@section('css')
    <style>
        .table td .label {
            margin-right: 10px;
        }
        .table td .label:last-child {
            margin-right: 0;
        }
        .table td .label .icon {
            vertical-align: middle;
            cursor: pointer;
        }
    </style>
    <!-- import ElementUI CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
@stop

@section('content')
    <div class="content">
        <form method="POST" action="" @submit.prevent="onSubmit">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ route('admin.dashboard.index') }}';"></i>

                        {{ __('admin::app.catalog.products.add-title') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('admin::app.catalog.products.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                @csrf()

                <?php $familyId = request()->input('family') ?>

                {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.general.before') !!}

                <accordian :title="'{{ __('admin::app.catalog.products.general') }}'" :active="true">
                    <div slot="body">

                        {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.general.controls.before') !!}

                        <div class="control-group" :class="[errors.has('type') ? 'has-error' : '']">
                            <label for="type" class="required">{{ __('admin::app.catalog.products.product-type') }}</label>
                            <select class="control" v-validate="'required'" id="type" name="type" {{ $familyId ? 'disabled' : '' }} data-vv-as="&quot;{{ __('admin::app.catalog.products.product-type') }}&quot;">

                                @foreach($productTypes as $key => $productType)
                                    <option value="{{ $key }}" {{ request()->input('type') == $productType['key'] ? 'selected' : '' }}>
                                        {{ $productType['name'] }}
                                    </option>
                                @endforeach

                            </select>

                            @if ($familyId)
                                <input type="hidden" name="type" value="{{ app('request')->input('type') }}"/>
                            @endif
                            <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('attribute_family_id') ? 'has-error' : '']">
                            <label for="attribute_family_id" class="required">{{ __('admin::app.catalog.products.familiy') }}</label>
                            <select class="control" v-validate="'required'" id="attribute_family_id" name="attribute_family_id" {{ $familyId ? 'disabled' : '' }} data-vv-as="&quot;{{ __('admin::app.catalog.products.familiy') }}&quot;">
                                <option value=""></option>
                                @foreach ($families as $family)
                                    <option value="{{ $family->id }}" {{ ($familyId == $family->id || old('attribute_family_id') == $family->id) ? 'selected' : '' }}>{{ $family->name }}</option>
                                    @endforeach
                            </select>

                            @if ($familyId)
                                <input type="hidden" name="attribute_family_id" value="{{ $familyId }}"/>
                            @endif
                            <span class="control-error" v-if="errors.has('attribute_family_id')">@{{ errors.first('attribute_family_id') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('sku') ? 'has-error' : '']">
                            <label for="sku" class="required">{{ __('admin::app.catalog.products.sku') }}</label>
                            <input type="text" v-validate="{ required: true, regex: /^[a-z0-9]+(?:-[a-z0-9]+)*$/ }" class="control" id="sku" name="sku" value="{{ request()->input('sku') ?: old('sku') }}" data-vv-as="&quot;{{ __('admin::app.catalog.products.sku') }}&quot;"/>
                            <span class="control-error" v-if="errors.has('sku')">@{{ errors.first('sku') }}</span>
                        </div>

                        {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.general.controls.after') !!}

                    </div>
                </accordian>

                {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.general.after') !!}

                @if ($familyId)

                    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.configurable_attributes.before') !!}

                    <accordian :title="'{{ __('admin::app.catalog.products.configurable-attributes') }}'" :active="true">
                        <div slot="body">

                            {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.configurable_attributes.controls.before') !!}

                            <div class="table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>{{ __('admin::app.catalog.products.attribute-header') }}</th>
                                            <th>{{ __('admin::app.catalog.products.attribute-option-header') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($configurableFamily->configurable_attributes as $attribute)
                                            <tr>
                                                <td>
                                                    {{ $attribute->admin_name }}
                                                </td>
                                                <td>
                                                    <custom-attributes-component :custom-attribute="{{json_encode($attribute)}}" :custom-attribute-options="{{json_encode($attribute->options)}}"></custom-attributes-component>
                                                </td>
                                                <td class="actions">
                                                    <i class="icon trash-icon"></i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>

                            {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.configurable_attributes.controls.after') !!}

                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.configurable_attributes.after') !!}
                @endif

            </div>

        </form>
    </div>
@stop

@push('scripts')
    <!-- import ElementUI JavaScript -->
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script>
        $(document).ready(function () {
            $('.label .cross-icon').on('click', function(e) {
                $(e.target).parent().remove();
            });

            $('.actions .trash-icon').on('click', function(e) {
                $(e.target).parents('tr').remove();
            });
        });
    </script>
    <script type="text/x-template" id="custom-attributes-template">
        <div>
            <el-tag
                :key="option"
                v-for="option in selectedAttributeOptions"
                closable
                :disable-transitions="false"
                @close="handleClose(option)">
                {% option.name %}
            </el-tag>
            <el-autocomplete
                class="input-new-tag"
                v-if="inputVisible"
                v-model="inputValue"
                :fetch-suggestions="querySearch"
                placeholder="Please Input"
                ref="saveTagInput"
                size="mini"
                @select="handleSelect"
            ></el-autocomplete>
            </el-input>
            <el-button v-else class="button-new-tag" size="small" @click="showInput">Add {% customAttribute.admin_name %} Option</el-button>
            <input type="hidden" v-for="option in selectedAttributeOptions" :name="'super_attributes[' + customAttribute.code + '][]'" :value="option.id" />
        </div>
    </script>
    <script>
        Vue.component('custom-attributes-component', {
            template: '#custom-attributes-template',
            delimiters: ['{%', '%}'],

            props: [
                'customAttribute',
                'customAttributeOptions',
            ],

            data() {
                return {
                    selectedAttributeOptions: [],
                    inputVisible: false,
                    inputValue: ''
                };
            },
            created() {
                console.log(this);
                this.options = this.loadAll();
            },
            methods: {
                handleClose(option) {
                    this.selectedAttributeOptions.splice(this.selectedAttributeOptions.indexOf(option), 1);
                },

                showInput() {
                    this.inputVisible = true;
                    this.$nextTick(_ => {
                        this.$refs.saveTagInput.$refs.input.focus();
                    });
                },

                querySearch(queryString, cb) {
                    var options = this.options;
                    var results = queryString ? options.filter(this.createFilter(queryString)) : options;
                    // call callback function to return suggestions
                    cb(results);
                },

                createFilter(queryString) {
                    return (option) => {
                        return (option.name.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
                    };
                },

                loadAll() {
                    return this.customAttributeOptions.map((option) => {
                        return {
                            "name": option.admin_name,
                            "value": option.admin_name, // Appears in autocomplete
                            "id" : option.id,
                        };
                    });
                },

                handleSelect(item) {
                    this.selectedAttributeOptions.push(item);
                    this.inputVisible = false;
                    this.inputValue = '';
                }
            }
        });
    </script>
@endpush