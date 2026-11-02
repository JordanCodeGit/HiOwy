@props(['dark' => false])

<span {{ $attributes->class(['auth-wordmark font-headline-lg text-[30px] leading-none font-bold tracking-[-0.04em] sm:text-[32px]', 'text-on-primary' => $dark, 'text-primary-container' => ! $dark]) }}>HiOwy<span class="{{ $dark ? 'text-tertiary-fixed' : 'text-secondary' }}">!</span></span>
