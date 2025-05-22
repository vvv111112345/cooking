document.addEventListener('DOMContentLoaded', function() {
    // Инициализация datepicker для полей с датой
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if(!input.value) {
            input.valueAsDate = new Date();
        }
    });
    
    // Маска для телефона
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            e.target.value = !x[2] ? x[1] : x[1] + ' (' + x[2] + ') ' + x[3] + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
        });
    });
    
    // Валидация форм
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if(!field.value.trim()) {
                    field.style.borderColor = 'red';
                    valid = false;
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            if(!valid) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
            }
        });
    });
    
    // Анимации
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.feature-card, .medical-form, .section');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if(elementPosition < screenPosition) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };
    
    // Инициализация анимаций
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll();
    
    // Добавляем стили для анимации
    const style = document.createElement('style');
    style.textContent = `
        .feature-card, .medical-form, .section {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
    `;
    document.head.appendChild(style);
});
// Печать конкретной записи
function printRecord(recordId) {
    // Открываем новое окно с содержимым для печати
    const printWindow = window.open('print_record.php?id=' + recordId, '_blank');

    // Альтернативный вариант - печать текущей страницы
    // window.location.href = 'print_record.php?id=' + recordId;
}
