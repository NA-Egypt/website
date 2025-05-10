<x-frontend.layout>
<h1 style="text-align: center;">{{ __('messages.NA') }}</h1><br />
<div style="background: #fffce0;border: 1px #dbd06a solid;text-align: center;padding: 10px;border-radius: 10px;">
<p style="margin-bottom: 0 !important">{{ __('messages.thewarning') }}</p>
</div>
<br />
<div class="warpper">
    <input class="radio" id="one" name="group" type="radio" checked>
    <input class="radio" id="two" name="group" type="radio">
    {{-- <input class="radio" id="three" name="group" type="radio"> --}}
  
    <div class="tabs">
      <label class="tab" id="one-tab" for="one">{{__('messages.aboutus') }}</label></label>
      <label class="tab" id="three-tab" for="two">{{ __('messages.wedorecover') }}</label>
      {{-- <label class="tab" id="two-tab" for="three">من هو المدمن؟</label> --}}
    </div>
  
    <div class="panels">
      <div class="panel" id="one-panel">
        <div class="panel-title">{{ __('messages.whatistheprogram') }}</div>
        <p>{{ __('messages.whatistheprogramtxt') }}</p>
      </div>
      <div class="panel" id="two-panel">
        <div class="panel-title">{{ __('messages.wedorecover') }}</div>
        <p>{{ __('messages.wedorecovertxt') }}</p>
      </div>
      {{-- <div class="panel" id="three-panel">
        <div class="panel-title">من هو المدمن؟</div>
        <p>معظمنا لا يحتاج للتفكير مرتين في هذا السؤال، فنحن نعلم! أن حياتنا وتفكيرنا تمركزا بالكامل في المخدّرات بشكل أو بآخر- في الحصول عليها وتعاطيها وإيجاد الطرق والوسائل للحصول على المزيد، فقد عشنا لنتعاطى وتعاطينا لنعيش. بمنتهى البساطة المدمن هو رجل أو إمرأة تسيطر المخدّرات على حياته، فنحن أناس في قبضة مرض مستمر ومتفاقم نهاياته دائماً لا تتغير: السجون أو المصحات أو الموت.</p>
      </div> --}}
    </div>
  </div>
  <div class="row justify-content-center">
    <div class="helpline-box">
        <h4><x-fas-headset style="width:16px; height:16px;"/>&NonBreakingSpace;{{ __('messages.helplines') }}</h4>
        <p dir="ltr"><a href="tel:+201006979198">+201006979198</a><br /><a href="tel:+201060933888">+201060933888</a>
            <br />
            <a href="https://wa.me/201060933888" target="_blank"><x-fab-whatsapp style="width:24px; height:24px;"/></a>
            <br />
        </p>
    </div>
    <div class="helpline-box">
        <h4><x-fas-headset style="width:16px; height:16px;"/>&NonBreakingSpace;{{ __('messages.helpline') }}</h4>
        <div>{{ __('messages.alexandria') }}</div>
        <p dir="ltr"><a href="tel:+201503884411">+201503884411</a><br />
            <a href="https://wa.me/201503884411" target="_blank"><x-fab-whatsapp style="width:24px; height:24px;"/></a>
            <br />
        </p>
    </div>
    <div class="helpline-box">
        <h4><x-fas-headset style="width:16px; height:16px;"/>&NonBreakingSpace;{{ __('messages.helpline') }}</h4> 
        <div>{{ __('messages.westgiza') }}</div>
        <p dir="ltr"><a href="tel:+201003694690">+201003694690</a><br />
            <a href="https://wa.me/201003694690" target="_blank"><x-fab-whatsapp style="width:24px; height:24px;"/></a>
            <br />
        </p>
    </div>
</div>
<div class="row justify-content-center">
<!--Calculator-->
<div class="calc-box">
    <div class="form-group">
        <h5><label for="date">{{ __('messages.calculator') }}</label></h5>
        <input type="date" class="form-control mb-3" onchange="setDate(this)">
    </div>
  <form name="myForm">
    <div class="form-group">
      <div class="input-group mb-3">
        <input type="text" class="form-control" name="Fyears" placeholder="{{ __('messages.years') }}" readonly>
        <div class="input-group-append">
            <span class="input-group-text" id="basic-addon3">{{ __('messages.years') }}</span>
        </div>
        </div>
        <div class="input-group mb-3">
        <input type="text" class="form-control" name="Fmonth" placeholder="{{ __('messages.months') }}" readonly>
            <div class="input-group-append">
                <span class="input-group-text" id="basic-addon3">{{ __('messages.months') }}</span>
            </div>
        </div>
        <div class="input-group mb-3">
        <input type="text" class="form-control" name="FR" placeholder="{{ __('messages.days') }}" readonly>
            <div class="input-group-append">
                <span class="input-group-text" id="basic-addon3">{{ __('messages.days') }}</span>
            </div>
        </div>
    </div>
    <button type="button" onclick="findTime(document.myForm)" class="btn btn-primary">{{ __('messages.calculate') }}</button>
  </form>
</div>
<!--Calculator End-->
</div>
  <script>
    let selectedDate = null;

    function setDate(input) {
    selectedDate = new Date(input.value);
    }

    function findTime(form) {
    if (!selectedDate) {
        alert("Please select a date first!");
        return;
    }

    const today = new Date();
    
    let totalYears = today.getFullYear() - selectedDate.getFullYear();
    let totalMonths = today.getMonth() - selectedDate.getMonth();
    let totalDays = today.getDate() - selectedDate.getDate();

    if (totalDays < 0) {
        totalMonths--;
        const previousMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        totalDays += previousMonth.getDate();
    }

    if (totalMonths < 0) {
        totalYears--;
        totalMonths += 12;
    }

    form.Fyears.value = totalYears;
    form.Fmonth.value = totalMonths;
    form.FR.value = totalDays;

    console.log(`Difference: ${totalYears} years / ${totalMonths} months / ${totalDays} days`);
    }

  </script>
</x-frontend.layout>
