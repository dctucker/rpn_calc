# rpn_calc
Reverse Polish Notation calculator written for PHP 7.0

Goals:
- Target PHP 7.0
- Minimize external dependencies
- 100% code coverage with unit tests

Structure:
- Symbol
  - Operand
    - Scalar
      - DecScalar
      - DegScalar
      - Constant
      - BaseScalar
        - HexScalar, OctScalar, BinScalar
    - Complex
      - PolarComplex
  - Operator
    - UnaryOperator
      - BaseOperator
        - Bin, Oct, Hex, Dec
      - Dump
      - BAnd, BOr, BNot, BXor, BShiftLeft, BShiftRight
      - Reciprocal, Negative, Ln, Intval, Frac, Round
      - TrigOperator
        - Sin, Cos, Tan, Degree, Radian
        - Mag, Arg, Conj, RealPart ImagPart
    - BinaryOperator
      - Push, Pop, Swap
      - Plus, Minus, Times, Divide, Modulo
      - Power, Sqrt, NthLog
    - interfaces
      - StackOperator
      - ScalarOperator
        - UnaryScalar
        - BinaryScalar
      - ComplexOperator
        - UnaryComplex
        - BinaryComplex
        - BinaryComplexScalar    
- SymbolFactory
  - OperatorFactory
  - OperandFactory
- Notation (traits)
  - Regex
    - Base
      - Decimal, Octal, Hexadecimal, Binary
    - Complex, PolarComplex
    - Degrees
    - Alphabetic
- Stack
  - GeneratorStack
  - NonCommutativeStack
- Calculator
- Parser
