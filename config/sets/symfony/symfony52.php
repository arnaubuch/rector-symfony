<?php

declare(strict_types=1);

use PHPStan\Type\ObjectType;

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\ClassConstFetch\RenameClassConstFetchRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\PropertyFetch\RenamePropertyRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Renaming\ValueObject\RenameClassAndConstFetch;
use Rector\Renaming\ValueObject\RenameProperty;
use Rector\Symfony\Rector\MethodCall\DefinitionAliasSetPrivateToSetPublicRector;
use Rector\Symfony\Rector\MethodCall\FormBuilderSetDataMapperRector;
use Rector\Symfony\Rector\MethodCall\ReflectionExtractorEnableMagicCallExtractorRector;
use Rector\Symfony\Rector\MethodCall\ValidatorBuilderEnableAnnotationMappingRector;
use Rector\Symfony\Rector\New_\PropertyAccessorCreationBooleanToFlagsRector;
use Rector\Symfony\Rector\New_\PropertyPathMapperToDataMapperRector;
use Rector\Symfony\Rector\StaticCall\BinaryFileResponseCreateToNewInstanceRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddParamTypeDeclaration;

# https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES);

    $services = $rectorConfig->services();

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#form
    $services->set(PropertyPathMapperToDataMapperRector::class);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#httpfoundation
    $services->set(BinaryFileResponseCreateToNewInstanceRector::class);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#mime
    $services->set(RenameMethodRector::class)
        ->configure([new MethodCallRename('Symfony\Component\Mime\Address', 'fromString', 'create')]);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#propertyaccess
    $services->set(PropertyAccessorCreationBooleanToFlagsRector::class);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#propertyinfo
    $services->set(ReflectionExtractorEnableMagicCallExtractorRector::class);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#security
    $services->set(RenameClassConstFetchRector::class)
        ->configure([
            new RenameClassAndConstFetch(
                'Symfony\Component\Security\Http\Firewall\AccessListener',
                'PUBLIC_ACCESS',
                'Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter',
                'PUBLIC_ACCESS'
            ),
        ]);

    $services->set(RenameMethodRector::class)
        ->configure([
            new MethodCallRename(
                'Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken',
                'setProviderKey',
                'setFirewallName'
            ),
            new MethodCallRename(
                'Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken',
                'getProviderKey',
                'getFirewallName'
            ),
            new MethodCallRename(
                'Symfony\Component\Security\Core\Authentication\Token\RememberMeToken',
                'setProviderKey',
                'setFirewallName'
            ),
            new MethodCallRename(
                'Symfony\Component\Security\Core\Authentication\Token\RememberMeToken',
                'getProviderKey',
                'getFirewallName'
            ),
            new MethodCallRename(
                'Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken',
                'setProviderKey',
                'setFirewallName'
            ),
            new MethodCallRename(
                'Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken',
                'getProviderKey',
                'getFirewallName'
            ),
            new MethodCallRename(
                'Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken',
                'setProviderKey',
                'setFirewallName'
            ),
            new MethodCallRename(
                'Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken',
                'getProviderKey',
                'getFirewallName'
            ),
            new MethodCallRename(
                'Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler',
                'setProviderKey',
                'setFirewallName'
            ),
            new MethodCallRename(
                'Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler',
                'getProviderKey',
                'getFirewallName'
            ),
        ]);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#dependencyinjection
    $services->set(DefinitionAliasSetPrivateToSetPublicRector::class);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#form
    $services->set(FormBuilderSetDataMapperRector::class);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#validator
    $services->set(ValidatorBuilderEnableAnnotationMappingRector::class);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#notifier
    $services->set(AddParamTypeDeclarationRector::class)
        ->configure([
            new AddParamTypeDeclaration(
                'Symfony\Component\Notifier\NotifierInterface',
                'send',
                1,
                new ObjectType('Symfony\Component\Notifier\Recipient\RecipientInterface')
            ),
            new AddParamTypeDeclaration(
                'Symfony\Component\Notifier\Notifier',
                'getChannels',
                1,
                new ObjectType('Symfony\Component\Notifier\Recipient\RecipientInterface')
            ),
            new AddParamTypeDeclaration(
                'Symfony\Component\Notifier\Channel\ChannelInterface',
                'notify',
                1,
                new ObjectType('Symfony\Component\Notifier\Recipient\RecipientInterface')
            ),
            new AddParamTypeDeclaration(
                'Symfony\Component\Notifier\Channel\ChannelInterface',
                'supports',
                1,
                new ObjectType('Symfony\Component\Notifier\Recipient\RecipientInterface')
            ),
        ]);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#notifier
    $services->set(AddParamTypeDeclarationRector::class)
        ->configure([
            new AddParamTypeDeclaration(
                'Symfony\Component\Notifier\Notification\ChatNotificationInterface',
                'asChatMessage',
                0,
                new ObjectType('Symfony\Component\Notifier\Recipient\RecipientInterface')
            ),
            new AddParamTypeDeclaration(
                'Symfony\Component\Notifier\Notification\EmailNotificationInterface',
                'asEmailMessage',
                0,
                new ObjectType('Symfony\Component\Notifier\Recipient\EmailRecipientInterface')
            ),
            new AddParamTypeDeclaration(
                'Symfony\Component\Notifier\Notification\SmsNotificationInterface',
                'asSmsMessage',
                0,
                new ObjectType('Symfony\Component\Notifier\Recipient\SmsRecipientInterface')
            ),
        ]);

    # https://github.com/symfony/symfony/blob/5.x/UPGRADE-5.2.md#security
    $services->set(RenamePropertyRector::class)
        ->configure([
            new RenameProperty(
                'Symfony\Component\Security\Http\RememberMe\AbstractRememberMeServices',
                'providerKey',
                'firewallName'
            ),
        ]);
};
