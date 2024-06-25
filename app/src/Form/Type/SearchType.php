<?php
/**
 * Search type.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *Class SearchType.
 */
class SearchType extends AbstractType
{
    public $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod(\Symfony\Component\HttpFoundation\Request::METHOD_GET)
            ->add('titleSearch', TextType::class, [
                'label' => 'label.titleSearch',
                'required' => false,
                'attr'  => [
                    'placeholder' => $this->translator->trans('action.searchTitle'),
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z0-9\s]*$/',
                        'message' => $this->translator->trans('message.invalid_characters'),
                    ]),
                ],
            ])
            ->add('authorSearch', TextType::class, [
                'label' => 'label.authorSearch',
                'required' => false,
                'attr'  => [
                    'placeholder' => $this->translator->trans('action.searchAuthor'),
                    'class'       => 'form-control',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z0-9\s]*$/',
                        'message' => $this->translator->trans('message.invalid_characters'),
                    ]),
                ],
            ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
        ]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
