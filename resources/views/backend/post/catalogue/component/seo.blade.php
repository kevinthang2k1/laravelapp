<div class="ibox">
    <div class="ibox-title">
        <h5>Cấu hình SEO</h5>
    </div>
    <div class="ibox-content">
        <div class="seo-container">
            <div class="meta-title">
                {{ 
                    (old('meta_title', ($postCatalogue->meta_title) ?? '')) ? old('meta_title', ($postCatalogue->meta_title) ?? '') : 'Bạn chưa có tiêu đề SEO' 
                }}
            </div>
            <div class="canonical">{{ (old('canonical')) ? config('app.url').old('canonical').config('apps.general.suffix') :'http://duong-dan-cua-ban.html' }}</div>
            <div class="meta-description">
                {{ 
                    (old('meta_description', ($postCatalogue->meta_title) ?? '')) ? old('meta_description', ($postCatalogue->meta_title) ?? '') : 'Bạn chưa có mô tả SEO' 
                }}
            </div>
        </div>

        <div class="seo-wrapper">
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <div class="Uk-flex Uk-flex-middle Uk-flex-space-betwween">
                                <span>Mô tả SEO</span>
                                <span class="count_meta-title">0 ký tự</span>
                            </div>
                        </label>
                        <input
                            type ="text"
                            name ="meta_title"
                            value ="{{ old('meta_title', ($postCatalogue->meta_title) ?? '') }}"
                            class="form-control"
                            placeholder=""
                            autocomplete="off"
                        >
                    </div>
                </div>
            </div>

            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <span>Từ khóa SEO</span>
                        </label>
                        <input
                            type ="text"
                            name ="meta_keyword"
                            value ="{{ old('meta_keyword', ($postCatalogue->meta_keyword) ?? '') }}"
                            class="form-control"
                            placeholder=""
                            autocomplete="off"
                        >
                    </div>
                </div>
            </div>

            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <div class="Uk-flex Uk-flex-middle Uk-flex-space-betwween">
                                <span>Mô tả SEO</span>
                                <span class="count_meta-description">0 ký tự</span>
                            </div>
                        </label>
                        <textarea
                            type ="text"
                            name ="meta_description"
                            class="form-control"
                            placeholder=""
                            autocomplete="off"
                        >{{ old('description', ($postCatalogue->meta_description) ?? '') }}
                    </textarea>
                    </div>
                </div>
            </div>

            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="" class="control-label text-left">
                            <span>Đường dẫn <span class="text-danger">*</span></span>
                        </label>
                        <div class="input-wrapper">
                            <input
                                type ="text"
                                name ="canonical"
                                value ="{{ old('canonical', ($postCatalogue->canonical) ?? '') }}"
                                class="form-control"
                                placeholder=""
                                autocomplete="off"
                            >
                            <span class="baseUrl">{{ config('app.url') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>