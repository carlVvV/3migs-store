// Product Form JavaScript - Stock Calculation
// File: resources/js/admin/product-form-stock.js

// Enhanced real-time stock calculation with comprehensive logging
function calculateTotalStock() {
    console.log('=== STOCK CALCULATION START ===');
    
    const sizeInputs = document.querySelectorAll('.size-stock-input');
    console.log('Found size inputs:', sizeInputs.length);
    
    if (sizeInputs.length === 0) {
        console.error('❌ NO SIZE INPUTS FOUND! Check if .size-stock-input class exists');
        return;
    }
    
    let totalStock = 0;
    let sizeBreakdown = {};
    let hasErrors = false;
    
    console.log('Processing each input:');
    
    sizeInputs.forEach((input, index) => {
        const rawValue = input.value;
        let value = parseInt(rawValue) || 0;
        
        // Extract size from input ID (more reliable than name)
        const size = input.id.replace('size_stock_', '');
        
        console.log(`  Input ${index + 1} (${size}):`);
        console.log(`    Raw value: "${rawValue}"`);
        console.log(`    Parsed value: ${value}`);
        console.log(`    Input element:`, input);
        
        // Validate and correct negative values
        if (value < 0) {
            console.log(`    ⚠️ Negative value detected, correcting to 0`);
            value = 0;
            input.value = 0;
            input.classList.add('border-red-500', 'bg-red-50');
            hasErrors = true;
            
            // Show temporary error message
            showInputError(input, 'Negative values not allowed');
        } else if (value > 0) {
            console.log(`    ✅ Valid positive value`);
            input.classList.remove('border-red-500', 'bg-red-50');
            input.classList.add('border-green-300', 'bg-green-50');
            
            // Remove error message if exists
            removeInputError(input);
        } else {
            console.log(`    ⚪ Zero or empty value`);
            // Empty or zero value - neutral state
            input.classList.remove('border-red-500', 'bg-red-50', 'border-green-300', 'bg-green-50');
            removeInputError(input);
        }
        
        totalStock += value;
        sizeBreakdown[size] = value;
        
        console.log(`    Running total: ${totalStock}`);
    });
    
    console.log('Final calculation:');
    console.log(`  Total Stock: ${totalStock}`);
    console.log(`  Size Breakdown:`, sizeBreakdown);
    console.log(`  Has Errors: ${hasErrors}`);
    
    // Update display with animation
    const displayElement = document.getElementById('total-stock-display');
    const inputElement = document.getElementById('total-stock-input');
    
    console.log('Looking for display elements:');
    console.log(`  Display element found:`, !!displayElement);
    console.log(`  Input element found:`, !!inputElement);
    
    if (displayElement && inputElement) {
        console.log('✅ Updating display elements');
        
        // Add animation class
        displayElement.classList.add('animate-pulse');
        
        // Update values
        const oldDisplayValue = displayElement.textContent;
        const oldInputValue = inputElement.value;
        
        displayElement.textContent = totalStock;
        inputElement.value = totalStock;
        
        console.log(`  Display updated: "${oldDisplayValue}" → "${totalStock}"`);
        console.log(`  Hidden input updated: "${oldInputValue}" → "${totalStock}"`);
        
        // Update size breakdown display
        updateSizeBreakdown(sizeBreakdown);
        
        // Update visual feedback
        updateVisualFeedback(totalStock, hasErrors);
        
        // Remove animation after short delay
        setTimeout(() => {
            displayElement.classList.remove('animate-pulse');
            console.log('Animation removed');
        }, 300);
    } else {
        console.error('❌ DISPLAY ELEMENTS NOT FOUND!');
        console.log('Available elements with similar IDs:');
        console.log('  total-stock-display:', document.getElementById('total-stock-display'));
        console.log('  total-stock-input:', document.getElementById('total-stock-input'));
    }
    
    console.log('=== STOCK CALCULATION END ===');
    return { totalStock, sizeBreakdown, hasErrors };
}

// Show input error message
function showInputError(input, message) {
    let errorElement = input.parentNode.querySelector('.input-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'input-error text-xs text-red-600 mt-1';
        input.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
    
    // Auto-remove error after 2 seconds
    setTimeout(() => {
        removeInputError(input);
    }, 2000);
}

// Remove input error message
function removeInputError(input) {
    const errorElement = input.parentNode.querySelector('.input-error');
    if (errorElement) {
        errorElement.remove();
    }
    input.classList.remove('border-red-500', 'bg-red-50');
}

// Update visual feedback based on stock levels
function updateVisualFeedback(totalStock, hasErrors) {
    const displayElement = document.getElementById('total-stock-display');
    const container = displayElement.closest('.bg-gradient-to-r');
    
    // Remove existing classes
    container.classList.remove('from-red-50', 'to-red-100', 'border-red-200');
    container.classList.remove('from-yellow-50', 'to-yellow-100', 'border-yellow-200');
    container.classList.remove('from-green-50', 'to-green-100', 'border-green-200');
    
    if (hasErrors) {
        container.classList.add('from-red-50', 'to-red-100', 'border-red-200');
        displayElement.classList.remove('text-blue-600', 'text-yellow-600', 'text-green-600');
        displayElement.classList.add('text-red-600');
    } else if (totalStock === 0) {
        container.classList.add('from-red-50', 'to-red-100', 'border-red-200');
        displayElement.classList.remove('text-blue-600', 'text-yellow-600', 'text-green-600');
        displayElement.classList.add('text-red-600');
    } else if (totalStock < 10) {
        container.classList.add('from-yellow-50', 'to-yellow-100', 'border-yellow-200');
        displayElement.classList.remove('text-blue-600', 'text-red-600', 'text-green-600');
        displayElement.classList.add('text-yellow-600');
    } else {
        container.classList.add('from-green-50', 'to-green-100', 'border-green-200');
        displayElement.classList.remove('text-blue-600', 'text-red-600', 'text-yellow-600');
        displayElement.classList.add('text-green-600');
    }
}

// Size breakdown display disabled per UI request
function updateSizeBreakdown() {
    const breakdownElement = document.getElementById('size-breakdown');
    if (breakdownElement) breakdownElement.style.display = 'none';
}

// Test function to verify calculation is working
window.testStockCalculation = function() {
    console.log('🧪 MANUAL TEST: Testing stock calculation...');
    calculateTotalStock();
};

// Comprehensive diagnostic function
window.diagnoseStockCalculation = function() {
    console.log('🔍 === DIAGNOSTIC REPORT ===');
    
    // Check if function exists
    console.log('1. Function exists:', typeof calculateTotalStock === 'function');
    
    // Check DOM elements
    const sizeInputs = document.querySelectorAll('.size-stock-input');
    console.log('2. Size inputs found:', sizeInputs.length);
    
    if (sizeInputs.length > 0) {
        console.log('3. Size input details:');
        sizeInputs.forEach((input, index) => {
            console.log(`   Input ${index + 1}:`, {
                id: input.id,
                name: input.name,
                value: input.value,
                className: input.className,
                element: input
            });
        });
    }
    
    // Check display elements
    const displayElement = document.getElementById('total-stock-display');
    const inputElement = document.getElementById('total-stock-input');
    console.log('4. Display elements:');
    console.log('   total-stock-display:', !!displayElement, displayElement);
    console.log('   total-stock-input:', !!inputElement, inputElement);
    
    // Check event listeners
    console.log('5. Event listeners check:');
    if (sizeInputs.length > 0) {
        const firstInput = sizeInputs[0];
        console.log('   First input onchange:', firstInput.onchange);
        console.log('   First input oninput:', firstInput.oninput);
        console.log('   First input onkeyup:', firstInput.onkeyup);
    }
    
    // Test calculation
    console.log('6. Running test calculation...');
    const result = calculateTotalStock();
    console.log('   Result:', result);
    
    console.log('🔍 === DIAGNOSTIC COMPLETE ===');
    return {
        functionExists: typeof calculateTotalStock === 'function',
        inputsFound: sizeInputs.length,
        displayElementsFound: !!(displayElement && inputElement),
        testResult: result
    };
};
