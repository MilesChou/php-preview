# Opcode example

參考 [PHP 7 Virtual Machine](https://nikic.github.io/2017/04/14/PHP-7-Virtual-machine.html#obtaining-opcode-dumps)，可以知道如何 dump opcode：

    php -d opcache.opt_debug_level=0x10000 test.php

然後參考 [`opt_debug_level`](https://www.php.net/manual/en/opcache.configuration.php#ini.opcache.opt_debug_level) 說明，可以知道還有一個 `0x20000` 參數是可以知道最佳化後的 opcode

最後可以使用下面的指令取得機器碼：

    php -d opcache.jit=1205 -dopcache.jit_debug=0x01 test.php

## 使用程式

[Fibonacci](fibonacci.php) 數列產生器遞迴版：

```php
function fibonacci_r($n)
{
    if ($n < 2) {
        return 1;
    }

    return fibonacci_r($n - 2) + fibonacci_r($n - 1);
}
```

## 開始 dump

先看未最佳化的（`opcache.opt_debug_level=0x10000`）

```
$_main:
     ; (lines=1, args=0, vars=0, tmps=0)
     ; (before optimizer)
     ; /source/extra/opcode/fibonacci.php:1-15
     ; return  [] RANGE[0..0]
0000 RETURN int(1)

fibonacci_r:
     ; (lines=15, args=1, vars=1, tmps=6)
     ; (before optimizer)
     ; /source/extra/opcode/fibonacci.php:7-14
     ; return  [] RANGE[0..0]
0000 CV0($n) = RECV 1
0001 T1 = IS_SMALLER CV0($n) int(2)
0002 JMPZ T1 0004
0003 RETURN int(1)
0004 INIT_FCALL_BY_NAME 1 string("fibonacci_r")
0005 T2 = SUB CV0($n) int(2)
0006 SEND_VAL_EX T2 1
0007 V3 = DO_FCALL_BY_NAME
0008 INIT_FCALL_BY_NAME 1 string("fibonacci_r")
0009 T4 = SUB CV0($n) int(1)
0010 SEND_VAL_EX T4 1
0011 V5 = DO_FCALL_BY_NAME
0012 T6 = ADD V3 V5
0013 RETURN T6
0014 RETURN null
LIVE RANGES:
     3: 0008 - 0012 (tmp/var)
```

然後再看最佳化的（`opcache.opt_debug_level=0x20000`）

```
$_main:
     ; (lines=1, args=0, vars=0, tmps=0)
     ; (after optimizer)
     ; /source/extra/opcode/fibonacci.php:1-15
0000 RETURN int(1)

fibonacci_r:
     ; (lines=14, args=1, vars=1, tmps=3)
     ; (after optimizer)
     ; /source/extra/opcode/fibonacci.php:7-14
0000 CV0($n) = RECV 1
0001 T1 = IS_SMALLER CV0($n) int(2)
0002 JMPZ T1 0004
0003 RETURN int(1)
0004 INIT_FCALL 1 144 string("fibonacci_r")
0005 T1 = SUB CV0($n) int(2)
0006 SEND_VAL T1 1
0007 V2 = DO_UCALL
0008 INIT_FCALL 1 144 string("fibonacci_r")
0009 T1 = SUB CV0($n) int(1)
0010 SEND_VAL T1 1
0011 V3 = DO_UCALL
0012 T1 = ADD V2 V3
0013 RETURN T1
LIVE RANGES:
     2: 0008 - 0012 (tmp/var)
```

最後看產出來的機器碼：

```
JIT$/source/extra/opcode/fibonacci.php: ; (/source/extra/opcode/fibonacci.php)
        sub $0x10, %rsp
        add $0x10, %rsp
        mov $ZEND_RETURN_SPEC_CONST_LABEL, %rax
        jmp *%rax

JIT$fibonacci_r: ; (/source/extra/opcode/fibonacci.php)
.ENTRY1:
        sub $0x10, %rsp
.L1:
        cmp $0x4, 0x58(%r14)
        jnz .L11
        cmp $0x2, 0x50(%r14)
        jge .L5
.L2:
        mov 0x10(%r14), %rcx
        test %rcx, %rcx
        jz .L3
        mov $0x1, (%rcx)
        mov $0x4, 0x8(%rcx)
.L3:
        mov 0x30(%r14), %rax
        mov $EG(current_execute_data), %rdx
        mov %rax, (%rdx)
        test $0x1, 0x59(%r14)
        jnz .L13
.L4:
        mov 0x28(%r14), %edi
        test $0x9e0000, %edi
        jnz JIT$$leave_function
        mov $EG(vm_stack_top), %rax
        mov %r14, (%rax)
        mov 0x30(%r14), %r14
        mov $EG(exception), %rax
        cmp $0x0, (%rax)
        mov (%r14), %r15
        jnz JIT$$leave_throw
        add $0x20, %r15
        add $0x10, %rsp
        jmp (%r15)
.L5:
        mov 0x18(%r14), %rax
        mov $EG(vm_stack_top), %r15
        mov (%r15), %r15
        mov $EG(vm_stack_end), %rdx
        mov (%rdx), %rdx
        sub %r15, %rdx
        cmp $0x90, %rdx
        jb .L15
        mov $EG(vm_stack_top), %rdx
        add $0x90, (%rdx)
        mov $0x0, 0x28(%r15)
        mov %rax, 0x18(%r15)
.L6:
        mov $0x0, 0x20(%r15)
        mov $0x1, 0x2c(%r15)
        cmp $0x4, 0x58(%r14)
        jnz .L16
        mov 0x50(%r14), %rax
        sub $0x2, %rax
        mov %rax, 0x50(%r15)
        mov $0x4, 0x58(%r15)
.L7:
        mov $0x40d2e898, (%r14)
        mov %r14, 0x30(%r15)
        mov $0x0, 0x8(%r15)
        lea 0x70(%r14), %rdx
        mov %rdx, 0x10(%r15)
        mov 0x40(%r14), %rdx
        mov %rdx, 0x40(%r15)
        mov $EG(current_execute_data), %rcx
        mov %r15, (%rcx)
        mov %r15, %r14
        mov $0x40d2e7d8, %r15
        jmp .L1
.ENTRY2:
        sub $0x10, %rsp
        mov 0x18(%r14), %rax
        mov $EG(vm_stack_top), %r15
        mov (%r15), %r15
        mov $EG(vm_stack_end), %rdx
        mov (%rdx), %rdx
        sub %r15, %rdx
        cmp $0x90, %rdx
        jb .L18
        mov $EG(vm_stack_top), %rdx
        add $0x90, (%rdx)
        mov $0x0, 0x28(%r15)
        mov %rax, 0x18(%r15)
.L8:
        mov $0x0, 0x20(%r15)
        mov $0x1, 0x2c(%r15)
        cmp $0x4, 0x58(%r14)
        jnz .L19
        mov 0x50(%r14), %rax
        sub $0x1, %rax
        mov %rax, 0x50(%r15)
        mov $0x4, 0x58(%r15)
.L9:
        mov $0x40d2e918, (%r14)
        mov %r14, 0x30(%r15)
        mov $0x0, 0x8(%r15)
        lea 0x80(%r14), %rdx
        mov %rdx, 0x10(%r15)
        mov 0x40(%r14), %rdx
        mov %rdx, 0x40(%r15)
        mov $EG(current_execute_data), %rcx
        mov %r15, (%rcx)
        mov %r15, %r14
        mov $0x40d2e7d8, %r15
        jmp .L1
.ENTRY3:
        sub $0x10, %rsp
        cmp $0x4, 0x78(%r14)
        jnz .L23
        cmp $0x4, 0x88(%r14)
        jnz .L21
        mov 0x70(%r14), %rax
        add 0x80(%r14), %rax
        jo .L22
        mov %rax, 0x60(%r14)
        mov $0x4, 0x68(%r14)
.L10:
        mov 0x10(%r14), %rcx
        test %rcx, %rcx
        jz .L28
        mov 0x60(%r14), %rdx
        mov %rdx, (%rcx)
        mov 0x68(%r14), %eax
        mov %eax, 0x8(%rcx)
        jmp .L3
.L11:
        cmp $0x5, 0x58(%r14)
        jnz .L12
        mov $0x2, %rax
        vcvtsi2sd %rax, %xmm0, %xmm0
        vucomisd 0x50(%r14), %xmm0
        jbe .L5
        jmp .L2
.L12:
        mov %r15, (%r14)
        lea 0x50(%r14), %rsi
        mov $0x40d2e788, %rdx
        lea 0x60(%r14), %rdi
        mov $compare_function, %rax
        call *%rax
        mov $EG(exception), %rax
        cmp $0x0, (%rax)
        jnz JIT$$exception_handler_undef
        cmp $0x0, 0x60(%r14)
        jge .L5
        jmp .L2
.L13:
        mov 0x50(%r14), %rdi
        sub $0x1, (%rdi)
        jnz .L14
        mov $0x40d2e818, (%r14)
        mov $rc_dtor_func, %rax
        call *%rax
        jmp .L4
.L14:
        test $0xfffffc10, 0x4(%rdi)
        jnz .L4
        mov $gc_possible_root, %rax
        call *%rax
        jmp .L4
.L15:
        mov $0x40d2e838, (%r14)
        mov $0x90, %edi
        mov %rax, %rsi
        mov $zend_jit_extend_stack_helper, %rax
        call *%rax
        mov %rax, %r15
        jmp .L6
.L16:
        cmp $0x5, 0x58(%r14)
        jnz .L17
        vmovsd 0x50(%r14), %xmm0
        mov $0x2, %rax
        vcvtsi2sd %rax, %xmm1, %xmm1
        vsubsd %xmm1, %xmm0, %xmm0
        vmovsd %xmm0, 0x50(%r15)
        mov $0x5, 0x58(%r15)
        jmp .L7
.L17:
        mov $0x40d2e858, (%r14)
        lea 0x50(%r15), %rdi
        lea 0x50(%r14), %rsi
        mov $0x40d2e788, %rdx
        mov $sub_function, %rax
        call *%rax
        mov $EG(exception), %rax
        cmp $0x0, (%rax)
        jnz JIT$$exception_handler
        jmp .L7
.L18:
        mov $0x40d2e8b8, (%r14)
        mov $0x90, %edi
        mov %rax, %rsi
        mov $zend_jit_extend_stack_helper, %rax
        call *%rax
        mov %rax, %r15
        jmp .L8
.L19:
        cmp $0x5, 0x58(%r14)
        jnz .L20
        vmovsd 0x50(%r14), %xmm0
        mov $0x1, %rax
        vcvtsi2sd %rax, %xmm1, %xmm1
        vsubsd %xmm1, %xmm0, %xmm0
        vmovsd %xmm0, 0x50(%r15)
        mov $0x5, 0x58(%r15)
        jmp .L9
.L20:
        mov $0x40d2e8d8, (%r14)
        lea 0x50(%r15), %rdi
        lea 0x50(%r14), %rsi
        mov $0x40d2e798, %rdx
        mov $sub_function, %rax
        call *%rax
        mov $EG(exception), %rax
        cmp $0x0, (%rax)
        jnz JIT$$exception_handler
        jmp .L9
.L21:
        cmp $0x5, 0x88(%r14)
        jnz .L25
        vcvtsi2sd 0x70(%r14), %xmm0, %xmm0
        vaddsd 0x80(%r14), %xmm0, %xmm0
        vmovsd %xmm0, 0x60(%r14)
        mov $0x5, 0x68(%r14)
        jmp .L10
.L22:
        vcvtsi2sd 0x70(%r14), %xmm0, %xmm0
        vcvtsi2sd 0x80(%r14), %xmm1, %xmm1
        vaddsd %xmm1, %xmm0, %xmm0
        vmovsd %xmm0, 0x60(%r14)
        mov $0x5, 0x68(%r14)
        jmp .L10
.L23:
        cmp $0x5, 0x78(%r14)
        jnz .L25
        cmp $0x5, 0x88(%r14)
        jnz .L24
        vmovsd 0x70(%r14), %xmm0
        vaddsd 0x80(%r14), %xmm0, %xmm0
        vmovsd %xmm0, 0x60(%r14)
        mov $0x5, 0x68(%r14)
        jmp .L10
.L24:
        cmp $0x4, 0x88(%r14)
        jnz .L25
        vcvtsi2sd 0x80(%r14), %xmm0, %xmm0
        vaddsd 0x70(%r14), %xmm0, %xmm0
        vmovsd %xmm0, 0x60(%r14)
        mov $0x5, 0x68(%r14)
        jmp .L10
.L25:
        mov %r15, (%r14)
        lea 0x60(%r14), %rdi
        lea 0x70(%r14), %rsi
        lea 0x80(%r14), %rdx
        mov $add_function, %rax
        call *%rax
        test $0x1, 0x79(%r14)
        jz .L26
        mov 0x70(%r14), %rdi
        sub $0x1, (%rdi)
        jnz .L26
        mov %r15, (%r14)
        mov $rc_dtor_func, %rax
        call *%rax
.L26:
        test $0x1, 0x89(%r14)
        jz .L27
        mov 0x80(%r14), %rdi
        sub $0x1, (%rdi)
        jnz .L27
        mov %r15, (%r14)
        mov $rc_dtor_func, %rax
        call *%rax
.L27:
        mov $EG(exception), %rax
        cmp $0x0, (%rax)
        jnz JIT$$exception_handler
        jmp .L10
.L28:
        test $0x1, 0x69(%r14)
        jz .L3
        mov 0x60(%r14), %rdi
        sub $0x1, (%rdi)
        mov $0x40d2e958, (%r14)
        mov $rc_dtor_func, %rax
        call *%rax
        jmp .L3
```

## Benchmark

這裡有個簡單的 [bench.php](bench.php) 程式可以看效能，用以下指令即可看出開或不開 JIT 的差異：

    php -d opcache.jit_buffer_size=0 bench.php
    php -d opcache.jit_buffer_size=64M -d opcache.jit=1205 bench.php

使用 Macbook Pro + Dual-Core Intel Core i7 3.1GHz + 16G 2400MHz DDR4 + Docker for Mac，執行結果如下：

```
/source # php -d opcache.jit_buffer_size=0 extra/opcode/bench.php 
1.5412089000019
/source # php -d opcache.jit_buffer_size=64M -d opcache.jit=1205 extra/opcode/bench.php 
1.061807799997
```
