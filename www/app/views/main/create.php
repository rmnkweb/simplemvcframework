<div class="inquiry">
    <form class="inquiryForm" id="inquiryForm" method="post" action="/inquiry/create/">
        <textarea class="hidden" name="image" id="inquiryFormImageText"></textarea>
        <div class="inquiryFormRow">
            <div class="inquiryFormRowTitle">
                ФИО
            </div>
            <div class="inquiryFormRowValue">
                <input type="text" name="fio" id="inquiryFormFioText" />
            </div>
        </div>
        <div class="inquiryFormRow">
            <div class="inquiryFormRowTitle">
                Телефон
            </div>
            <div class="inquiryFormRowValue">
                <input type="text" name="phone" placeholder="+71234567890" />
            </div>
        </div>
        <div id="step1Blocks">
            <div class="inquiryFormRow">
                <button class="inquiryFormRowButton" id="createImageButton">Создать справку</button>
            </div>
        </div>
        <div id="step2Blocks" class="hidden">
            <div class="inquiryFormRow">
                <div class="inquiryFormRowTitle">
                    Изображение <a href="" id="newImageGuid" target="_blank"></a> создано!
                </div>
            </div>
            <div class="inquiryFormRow">
                <button class="inquiryFormRowButton" id="sendMessageButton">Отправить СМС</button>
            </div>
        </div>
    </form>
    <form class="hidden" id="inquirySendSmsForm" method="post" action="/sms/send/">
        <input type="text" name="phone" id="inquirySendSmsFormPhone" />
        <input type="text" name="text" id="inquirySendSmsFormText" />
    </form>
</div>
<canvas id="inquiryGeneratorCanvas" class="hidden"></canvas>