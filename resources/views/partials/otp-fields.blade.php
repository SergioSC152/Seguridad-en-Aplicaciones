<fieldset id="otp-fields"><legend class="fs-6 fw-semibold">Código de seis dígitos</legend><div class="d-flex gap-2 justify-content-center my-3">
@for($i=0;$i<6;$i++)<input class="form-control text-center" name="code_digits[{{ $i }}]" type="text" inputmode="numeric" pattern="[0-9]" maxlength="1" style="width:48px;min-width:0;letter-spacing:0;padding:.5rem" aria-label="Dígito {{ $i+1 }}" @if($i===0)autocomplete="one-time-code" autofocus @else autocomplete="off" @endif required>@endfor
</div><input type="hidden" name="code" id="code"></fieldset>
<script>
    const otpBoxes=Array.from(document.querySelectorAll('#otp-fields input[type=text]'));
    function collectOtp(){document.getElementById('code').value=otpBoxes.map(box=>box.value).join('');}
    otpBoxes.forEach((box,index)=>{
        box.addEventListener('input',()=>{box.value=box.value.replace(/\D/g,'').slice(-1);collectOtp();if(box.value&&index<5)otpBoxes[index+1].focus();});
        box.addEventListener('keydown',event=>{if(event.key==='Backspace'&&!box.value&&index>0)otpBoxes[index-1].focus();});
        box.addEventListener('paste',event=>{const digits=event.clipboardData.getData('text').replace(/\D/g,'').slice(0,6);if(!digits)return;event.preventDefault();digits.split('').forEach((digit,i)=>{if(otpBoxes[index+i])otpBoxes[index+i].value=digit;});collectOtp();otpBoxes[Math.min(index+digits.length,5)].focus();});
    });
</script>
