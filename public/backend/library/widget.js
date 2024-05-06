(function($){
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf-token"]').attr('content');
    let typingTimer;
    let doneTyingInterval = 100;//1s

    
    HT.searchModel = () => {
        $(document).on('keyup', '.search-model', function(e){
            e.preventDefault()
            let _this = $(this)
            if($('input[type=radio]:checked').length === 0){
                alert('Bạn chưa chọn Model');
                _this.val('')
                return false;
            }

            let keyword = _this.val()
            let option = {
                model: $('input[type=radio]:checked').val(),
                keyword : keyword
            }
            HT.sendAjax(option)
        })
    }
    //xem tất cả dữ liệu
    HT.chooseModel = () => {
        $(document).on('change', '.input-radio', function(){
            let _this = $(this)
            let option = {
                model: _this.val(),
                keyword : $('.search-model').val()
            }
            $('.search-model-result').html('');
            if(option.keyword.length >= 2){
                HT.sendAjax(option)
            }
        })
    }
    //end

    HT.sendAjax = (option) => {
        clearTimeout(typingTimer);
            typingTimer = setTimeout(function(){
                $.ajax({
                    url: 'ajax/dashboard/findModelObject',
                    type: 'GET',
                    data: option,
                    dataType: 'json',
                    success:function(res){
                        let html = HT.renderSearchResult(res)
                        if(html.length){
                            $('.ajax-search-result').html(html).show()
                        }else{
                            $('.ajax-search-result').html(html).hide()
                        }
                    },
                    beforeSend: function() {
                        $('.ajax-search-result').html('').hide()
                    },
                    
                });
            }, doneTyingInterval)
    }

    HT.renderSearchResult = (data) => {
        let html = ''
        if(data.length){
            for(let i = 0; i < data.length; i++){
                
                let flag = ($('#model-'+data[i].id).length) ? 1 : 0;
                let setChecked = ($('#model-'+data[i].id).length) ? HT.setChecked() : ''


                html +=  `<button 
                            class="ajax-search-item" 
                            data-flag="${flag}" 
                            data-canonical="${data[i].languages[0].pivot.canonical}" 
                            data-image="${data[i].image}" 
                            data-name="${data[i].languages[0].pivot.name}" 
                            data-id="${data[i].id}"
                        >
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <span>${data[i].languages[0].pivot.name}</span>
                    <div class="auto-icon">
                        ${setChecked}
                    </div>
                </div>
            </button>`
            }
        }
        return html
    }

    HT.setChecked = () => {
        return '<img src="backend/img/icons8-close-52.png" alt="">'
    }

    HT.unfocusSearchBox = () => {
        $(document).on('click', 'html', function(e){
            if(!$(e.target).hasClass('search-model-box') || !$(e.target).hasClass('search-model')){
                $('.ajax-search-result').html('')
            }
        })

        $(document).on('click', '.ajax-search-result', function(e){
            e.stopPropagation();
        })
    }

    HT.addModel = () => {
        $(document).on('click', '.ajax-search-item', function(e){
            e.preventDefault()
            let _this = $(this)
            let data = _this.data()
            let html = HT.modelTemplate(data)
            let flag = _this.attr('data-flag')
            if(flag == 0){
                _this.find('.auto-icon').html(HT.setChecked())
                _this.attr('data-flag', 1)
                $('.search-model-result').append(HT.modelTemplate(data)).show()
            }else{
                $('#model-'+data.id).remove()
                _this.find('.auto-icon').html('')
                _this.attr('.data-flag', 0)
            }
        })
    }

    HT.modelTemplate = (data) => {
        let html = `<div class="search-result-item" id="model-${data.id}" data-modelid="${data.id}">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="uk-flex uk-flex-middle">
                <span class="image img-cover"><img src="${data.image}" alt=""></span>
                <span class="name">${data.name}</span>
                <div class="hidden">
                    <input type="text" name="modelItem[id][]" value="${data.id}">
                    <input type="text" name="modelItem[name][]" value="${data.name}">
                    <input type="text" name="modelItem[image][]" value="${data.image}">
                </div>
            </div>
            <div class="deleted">
                <span class="image img-cover"><img src="backend/img/icons8-close-52.png" alt=""></span>
            </div>
        </div>
    </div>`
    return html
    }

    HT.removeModel = () => {
        $(document).on('click','.deleted', function(){
            let _this = $(this)
            _this.parents('.search-result-item').remove()
        })
    }

    $(document).ready(function(){
        HT.searchModel()
        HT.chooseModel()
        HT.unfocusSearchBox()
        HT.addModel()
        HT.removeModel()
    });
})(jQuery)
