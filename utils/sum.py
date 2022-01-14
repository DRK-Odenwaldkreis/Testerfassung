


def get_last_sum(number):
    x = sum(int(digit) for digit in str(number))   
    if x < 10:
        return x
    else:
        return get_last_sum(x)