# rpn_calc
Reverse Polish Notation calculator written for PHP 7.0

## Goals
- Target PHP 7.0
- Minimize external dependencies
- 100% code coverage with unit tests

## Code coverage report

https://htmlpreview.github.io/?https://github.com/dctucker/rpn_calc/blob/master/coverage/index.html

## Class Structure

- App\Symbol
  - App\Operand
    - App\Operands\Scalar
      - DecScalar
      - DegScalar
      - Constant
      - BaseScalar
        - HexScalar, OctScalar, BinScalar
    - App\Operands\Complex
      - PolarComplex
  - App\Operator
    - App\Operators\UnaryOperator
      - BaseOperator
        - Bin, Oct, Hex, Dec
      - Dump
      - BAnd, BOr, BNot, BXor, BShiftLeft, BShiftRight
      - Reciprocal, Negative, Ln, Intval, Frac, Round
      - TrigOperator
        - Sin, Cos, Tan, Degree, Radian
        - Mag, Arg, Conj, RealPart ImagPart
    - App\Operators\BinaryOperator
      - Push, Pop, Swap
      - Plus, Minus, Times, Divide, Modulo
      - Power, Sqrt, NthLog
    - App\Operators\(interfaces)
      - StackOperator
      - ScalarOperator
        - UnaryScalar
        - BinaryScalar
      - ComplexOperator
        - UnaryComplex
        - BinaryComplex
        - BinaryComplexScalar    
- App\SymbolFactory
  - OperatorFactory
  - OperandFactory
- App\Notations\Notation (interface)
  - Regex
    - Base
      - Decimal, Octal, Hexadecimal, Binary
    - Complex, PolarComplex
    - Degrees
    - Alphabetic
- App\Stack (interface)
  - GeneratorStack
  - NonCommutativeStack
- App\Calculator
- App\Parser
