@props(['targetInput' => '', 'showModal' => false])

<div x-data="posCalculator()" class="relative">
    <!-- Calculator Button -->
    <button @click="openCalculator()" 
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
        <i class="fas fa-calculator"></i>
        <span>Calculator</span>
    </button>

    <!-- Calculator Modal -->
    <div x-show="showCalculator" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center"
         @click.self="closeCalculator()">
        
        <div class="bg-white rounded-lg shadow-xl w-80 max-w-sm mx-4" @click.stop>
            <!-- Calculator Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">POS Calculator</h3>
                <button @click="closeCalculator()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Calculator Display -->
            <div class="p-4">
                <div class="bg-gray-100 rounded-lg p-4 mb-4">
                    <div class="text-right">
                        <div class="text-sm text-gray-600" x-text="expression"></div>
                        <div class="text-2xl font-bold text-gray-900" x-text="display"></div>
                    </div>
                </div>

                <!-- Calculator Buttons -->
                <div class="grid grid-cols-4 gap-2">
                    <!-- Row 1 -->
                    <button @click="clear()" class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-lg font-medium">C</button>
                    <button @click="clearEntry()" class="bg-gray-500 hover:bg-gray-600 text-white p-3 rounded-lg font-medium">CE</button>
                    <button @click="backspace()" class="bg-gray-500 hover:bg-gray-600 text-white p-3 rounded-lg font-medium">
                        <i class="fas fa-backspace"></i>
                    </button>
                    <button @click="inputOperator('/')" class="bg-orange-500 hover:bg-orange-600 text-white p-3 rounded-lg font-medium">÷</button>

                    <!-- Row 2 -->
                    <button @click="inputNumber('7')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">7</button>
                    <button @click="inputNumber('8')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">8</button>
                    <button @click="inputNumber('9')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">9</button>
                    <button @click="inputOperator('*')" class="bg-orange-500 hover:bg-orange-600 text-white p-3 rounded-lg font-medium">×</button>

                    <!-- Row 3 -->
                    <button @click="inputNumber('4')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">4</button>
                    <button @click="inputNumber('5')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">5</button>
                    <button @click="inputNumber('6')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">6</button>
                    <button @click="inputOperator('-')" class="bg-orange-500 hover:bg-orange-600 text-white p-3 rounded-lg font-medium">-</button>

                    <!-- Row 4 -->
                    <button @click="inputNumber('1')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">1</button>
                    <button @click="inputNumber('2')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">2</button>
                    <button @click="inputNumber('3')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">3</button>
                    <button @click="inputOperator('+')" class="bg-orange-500 hover:bg-orange-600 text-white p-3 rounded-lg font-medium">+</button>

                    <!-- Row 5 -->
                    <button @click="inputNumber('0')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium col-span-2">0</button>
                    <button @click="inputNumber('.')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-3 rounded-lg font-medium">.</button>
                    <button @click="calculate()" class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-lg font-medium">=</button>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex space-x-2">
                    <button @click="insertResult()" 
                            class="flex-1 bg-maroon hover:bg-maroon-dark text-white py-2 px-4 rounded-lg font-medium transition-colors">
                        Insert Result
                    </button>
                    <button @click="closeCalculator()" 
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function posCalculator() {
    return {
        showCalculator: false,
        display: '0',
        expression: '',
        operator: null,
        previousNumber: null,
        waitingForNumber: false,
        targetInput: '{{ $targetInput }}',

        openCalculator() {
            this.showCalculator = true;
            this.clear();
        },

        closeCalculator() {
            this.showCalculator = false;
        },

        inputNumber(num) {
            if (this.waitingForNumber) {
                this.display = num;
                this.waitingForNumber = false;
            } else {
                this.display = this.display === '0' ? num : this.display + num;
            }
        },

        inputOperator(op) {
            if (this.operator && !this.waitingForNumber) {
                this.calculate();
            }
            
            this.previousNumber = parseFloat(this.display);
            this.operator = op;
            this.waitingForNumber = true;
            this.expression = this.display + ' ' + op;
        },

        calculate() {
            if (this.operator && this.previousNumber !== null) {
                const currentNumber = parseFloat(this.display);
                let result;

                switch (this.operator) {
                    case '+':
                        result = this.previousNumber + currentNumber;
                        break;
                    case '-':
                        result = this.previousNumber - currentNumber;
                        break;
                    case '*':
                        result = this.previousNumber * currentNumber;
                        break;
                    case '/':
                        result = currentNumber !== 0 ? this.previousNumber / currentNumber : 0;
                        break;
                    default:
                        return;
                }

                this.display = result.toString();
                this.expression = this.previousNumber + ' ' + this.operator + ' ' + currentNumber + ' =';
                this.operator = null;
                this.previousNumber = null;
                this.waitingForNumber = true;
            }
        },

        clear() {
            this.display = '0';
            this.expression = '';
            this.operator = null;
            this.previousNumber = null;
            this.waitingForNumber = false;
        },

        clearEntry() {
            this.display = '0';
        },

        backspace() {
            if (this.display.length > 1) {
                this.display = this.display.slice(0, -1);
            } else {
                this.display = '0';
            }
        },

        insertResult() {
            if (this.targetInput) {
                const targetElement = document.getElementById(this.targetInput);
                if (targetElement) {
                    targetElement.value = this.display;
                    targetElement.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
            this.closeCalculator();
        }
    }
}
</script>
